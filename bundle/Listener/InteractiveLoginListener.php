<?php
/**
 * NovaeZLDAPAuthenticator Bundle.
 *
 * @package   Novactive\Bundle\eZLDAPAuthenticatorBundle
 *
 * @author    Novactive
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZLdapAuthenticatorBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZLDAPAuthenticatorBundle\Listener;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\MVC\Symfony\Event\InteractiveLoginEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Translation\TranslatorInterface;

class InteractiveLoginListener implements EventSubscriberInterface
{
    /** @var Repository */
    private $repository;

    /** @var Ldap */
    private $ldap;

    /** @var array */
    private $config;

    /** @var LoggerInterface */
    private $logger;

    /** @var TranslatorInterface */
    private $translator;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(
        Repository $repository,
        Ldap $ldap,
        $config,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        TokenStorageInterface $tokenStorage
    ) {
        $this->repository   = $repository;
        $this->ldap         = $ldap;
        $this->config       = $config;
        $this->logger       = $logger;
        $this->translator   = $translator;
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MVCEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
        ];
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $userService = $this->repository->getUserService();
        $username    = $event->getAuthenticationToken()->getUsername();

        try {
            $event->setApiUser($userService->loadUserByLogin($username));
        } catch (NotFoundException $exception) {
            $baseDn       = $this->config['ldap']['base_dn'];
            $searchDn     = $this->config['ldap']['search']['search_dn'];
            $password     = $this->config['ldap']['search']['search_password'];
            $queryString  = $this->config['ldap']['search']['search_string'];
            $uidKey       = $this->config['ldap']['search']['uid_key'];
            $passwordAttr = $this->config['ldap']['search']['password_attribute'];
            $targetGroup  = $this->config['ez_user']['target_usergroup'];
            $emailAttr    = $this->config['ez_user']['email_attr'];
            $attributes   = $this->config['ez_user']['attributes'];

            // Login to LDAP server and get user attributes
            $this->ldap->bind($searchDn, $password);
            $queryString = str_replace(['{username}', '{uid_key}'], [$username, $uidKey], $queryString);
            $query       = $this->ldap->query($baseDn, $queryString);
            $results     = $query->execute();

            // Prepare user details
            $propertyAccessor = PropertyAccess::createPropertyAccessor();

            $ldapUser = $results->toArray()[0]->getAttributes();
            $email    = $propertyAccessor->getValue($ldapUser, "[$emailAttr][0]");
            $password = $propertyAccessor->getValue($ldapUser, "[$passwordAttr][0]");

            $user = $userService->newUserCreateStruct($username, $email, $password, 'fre-FR');

            foreach ($attributes as $attr) {
                $value = $propertyAccessor->getValue($ldapUser, "[{$attr['ldap_attr']}][0]");
                if ($value) {
                    $user->setField($attr['user_attr'], $value);
                } else {
                    // TODO: look for better solution.
                    // The only way to show the message to the user (as I know) -
                    // to throw BadCredentialsException
                    throw new BadCredentialsException(
                        $this->translator->trans(
                            'nullAttributeError',
                            [
                                '%username%'  => $username,
                                '%attribute%' => $attr['ldap_attr'],
                            ]
                        )
                    );
                }
            }

            $user->enabled = true;

            try {
                $group = $userService->loadUserGroup($targetGroup);
            } catch (NotFoundException $exception) {
                throw new \Exception($this->translator->trans('wrongGroupError', ['%id%' => $targetGroup]));
            }

            // Create new user under 'admin' user
            $this->repository->sudo(
                function (Repository $repository) use ($user, $group, $event, $userService, $username) {
                    try {
                        $event->setApiUser($userService->loadUserByLogin('admin'));
                        $repository->getUserService()->createUser($user, [$group]);
                    } catch (NotFoundException $exception) {
                        $this->tokenStorage->setToken(null);
                        $event->getRequest()->getSession()->invalidate();
                        throw new \Exception(
                            $this->translator->trans('userNotCreated', ['%username%' => $username]),
                            0,
                            $exception
                        );
                    } catch (\Exception $exception) {
                        $this->tokenStorage->setToken(null);
                        $event->getRequest()->getSession()->invalidate();
                        throw $exception;
                    }
                }
            );

            $event->setApiUser($userService->loadUserByLogin($username));
        }
    }
}

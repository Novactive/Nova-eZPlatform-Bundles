<?php
/**
 * NovaeZLdapAuthenticatorBundle.
 *
 * @package   NovaeZLdapAuthenticatorBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZLdapAuthenticatorBundle/blob/master/LICENSE
 */

namespace Novactive\EzLdapAuthenticator\EventListener;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\MVC\Symfony\Event\InteractiveLoginEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\PropertyAccess\PropertyAccess;

class InteractiveLoginListener implements EventSubscriberInterface
{
    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var  Symfony\Component\Ldap\Ldap*/
    private $ldap;

    /** @var array */
    private $config;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(Repository $repository, Ldap $ldap, $config)
    {
        $this->repository = $repository;
	    $this->userService = $this->repository->getUserService();
        $this->ldap = $ldap;
        $this->config = $config;
    }

    public static function getSubscribedEvents()
    {
        return [
            MVCEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
        ];
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
	    $userService = $this->repository->getUserService();
        $username = $event->getAuthenticationToken()->getUsername();

        try {
	        $event->setApiUser($userService->loadUserByLogin($username));
        } catch (\eZ\Publish\API\Repository\Exceptions\NotFoundException $exception) {
        	$searchDn       = $this->config['search_dn'];
        	$password       = $this->config['search_password'];
        	$queryString    = $this->config['query_string'];
        	$baseDn         = $this->config['base_dn'];
        	$targetGroup    = $this->config['target_usergroup'];
        	$uidKey         = $this->config['uid_key'];

        	// Login to LDAP server and get user attributes
        	$this->ldap->bind($searchDn, $password);
            $queryString = str_replace("{username}", $username, $queryString);
            $queryString = str_replace("{uid_key}", $uidKey, $queryString);
        	$query = $this->ldap->query($baseDn, $queryString);
	        $results = $query->execute();

	        // Prepare user details
            $propertyAccessor = PropertyAccess::createPropertyAccessor();

	        $ldapUser = $results->toArray()[0]->getAttributes();
	        $email      = $propertyAccessor->getValue($ldapUser, '[mail][0]');
            $password   = $propertyAccessor->getValue($ldapUser, '[userPassword][0]');
            $firstName  = $propertyAccessor->getValue($ldapUser, '[givenName][0]') ?: $username;
            $lastName   = $propertyAccessor->getValue($ldapUser, '[sn][0]');

	        $user = $userService->newUserCreateStruct($username, $email, $password, "fre-FR");
	        $user->setField("first_name", $firstName);
	        if ($lastName) {
                $user->setField("last_name", $lastName);
            }
	        $user->enabled = true;

	        $group = $userService->loadUserGroup($targetGroup);

	        // Create the user
	        $this->repository->sudo(function (Repository $repository) use ($user, $group, $event, $userService) {
		        $event->setApiUser($userService->loadUserByLogin('admin'));
		        $userService->createUser($user, [$group]);
	        });

	        $event->setApiUser($userService->loadUserByLogin($username));
        }

    }
}

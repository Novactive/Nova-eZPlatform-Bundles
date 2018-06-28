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

use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\MVC\Symfony\Event\InteractiveLoginEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;
use \eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Ldap\Ldap;

class InteractiveLoginListener implements EventSubscriberInterface
{
    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var  Symfony\Component\Ldap\Ldap*/
    private $ldap;

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container, Repository $repository, Ldap $ldap)
    {
    	$this->container = $container;
        $this->repository = $repository;
	    $this->userService = $this->repository->getUserService();
        $this->ldap = $ldap;
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
        	$searchDn       = $this->container->getParameter("ldap_auth_search_dn");
        	$password       = $this->container->getParameter("ldap_auth_search_password");
        	$queryString    = $this->container->getParameter("ldap_auth_query_string");
        	$baseDn         = $this->container->getParameter("ldap_auth_base_dn");
        	$targetGroup    = $this->container->getParameter("ldap_auth_target_usergroup");

        	// Login to LDAP server and get user attributes
        	$this->ldap->bind($searchDn, $password);
        	$query = $this->ldap->query($baseDn, str_replace("{username}", $username, $queryString));
	        $results = $query->execute();

	        // Prepare user details
	        $ldapUser = $results->toArray()[0]->getAttributes();
	        $email = $ldapUser["mail"][0] ?? "email@example.com";
	        $password = $ldapUser["userPassword"][0];

	        $user = $userService->newUserCreateStruct($username, $email, $password, "fre-FR");
	        $user->setField("first_name", $ldapUser["givenName"][0] ?? $username);
	        $user->setField("last_name", $ldapUser["sn"][0] ?? $username);
	        $user->enabled = true;

	        $group = $userService->loadUserGroup($targetGroup);

	        // Create the user
	        $this->repository->sudo(function (Repository $repository) use ($user, $group) {
		        $event->setApiUser($userService->loadUserByLogin('admin'));
	        	try {
			        $userService->createUser($user, [$group]);
		        } catch (\eZ\Publish\Core\Base\Exceptions\ContentFieldValidationException $exception) {
	        		dump($exception);
		        }
	        });

	        $event->setApiUser($userService->loadUserByLogin($username));
        }

    }
}

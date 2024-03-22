<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaSaml\Security\Saml;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\User as APIUser;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Security\User;
use Ibexa\Core\MVC\Symfony\Security\User\BaseProvider;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SamlUserProvider implements UserProviderInterface
{
    public const LOAD_METHOD_LOGIN = 'loadUserByLogin';
    public const LOAD_METHOD_EMAIL = 'loadUserByEmail';

    protected BaseProvider $baseProvider;
    protected PermissionResolver $permissionResolver;
    protected UserService $userService;
    protected ConfigResolverInterface $configResolver;

    public function __construct(
        BaseProvider $baseProvider,
        PermissionResolver $permissionResolver,
        UserService $userService,
        ConfigResolverInterface $configResolver
    ) {
        $this->baseProvider = $baseProvider;
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
        $this->configResolver = $configResolver;
    }

    public function refreshUser(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        return $this->baseProvider->refreshUser($user);
    }

    public function supportsClass(string $class)
    {
        return $this->baseProvider->supportsClass($class);
    }

    public function loadUserByIdentifier(string $identifier)
    {
        $user = $this->loadUser($identifier);
        $this->permissionResolver->setCurrentUserReference($user->getAPIUser());

        return $user;
    }

    protected function createSecurityUser(APIUser $apiUser): User
    {
        return new User($apiUser, ['ROLE_USER']);
    }

    public function loadUser(string $identifier)
    {
        try {
            $loadMethod = $this->configResolver->getParameter('user_load_method', 'almaviacx.saml.config');

            return $this->createSecurityUser(
                $this->userService->{$loadMethod}($identifier)
            );
        } catch (NotFoundException $e) {
            return $this->baseProvider->loadUserByUsername($identifier);
        }
    }

    public function loadUserByUsername($username)
    {
        return $this->baseProvider->loadUserByUsername($username);
    }
}

<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaSaml\Security\Saml;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Core\MVC\Symfony\Security\User\BaseProvider;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SamlUserProvider implements UserProviderInterface
{
    protected BaseProvider $baseProvider;
    protected PermissionResolver $permissionResolver;

    public function __construct(BaseProvider $baseProvider, PermissionResolver $permissionResolver)
    {
        $this->baseProvider = $baseProvider;
        $this->permissionResolver = $permissionResolver;
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
        $user = $this->baseProvider->loadUserByUsername($identifier);
        $this->permissionResolver->setCurrentUserReference($user->getAPIUser());

        return $user;
    }

    public function loadUserByUsername(string $username)
    {
        return $this->baseProvider->loadUserByUsername($username);
    }
}

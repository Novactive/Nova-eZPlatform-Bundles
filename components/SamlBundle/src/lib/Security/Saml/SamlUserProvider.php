<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaSaml\Security\Saml;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Core\MVC\Symfony\Security\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Ibexa\Core\MVC\Symfony\Security\User\BaseProvider;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SamlUserProvider implements UserProviderInterface
{
    protected SamlExceptionLogger $ssoExceptionLogger;

    protected UserProviderInterface $baseProvider;

    /**
     * @param \AlmaviaCX\Bundle\IbexaSaml\Security\Saml\SamlExceptionLogger $ssoExceptionLogger
     * @param \Symfony\Component\Security\Core\User\UserProviderInterface $baseProvider
     */
    public function __construct( SamlExceptionLogger $ssoExceptionLogger, UserProviderInterface $baseProvider )
    {
        $this->ssoExceptionLogger = $ssoExceptionLogger;
        $this->baseProvider = $baseProvider;
    }

    public function refreshUser( \Symfony\Component\Security\Core\User\UserInterface $user )
    {
        return $this->baseProvider->refreshUser($user);
    }

    public function supportsClass( string $class )
    {
        return $this->baseProvider->supportsClass($class);
    }

    public function loadUserByIdentifier($identifier): UserInterface
    {
        return $this->baseProvider->loadUserByUsername($identifier);
    }

    public function loadUserByUsername( string $username )
    {
        return $this->baseProvider->loadUserByUsername($username);
    }
}

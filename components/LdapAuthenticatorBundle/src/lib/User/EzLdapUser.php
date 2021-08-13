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

namespace Novactive\eZLDAPAuthenticator\User;

use Symfony\Component\Security\Core\User\UserInterface;

class EzLdapUser implements UserInterface
{
    /** @var string */
    protected $username;

    /** @var string */
    protected $email;

    /** @var array */
    protected $attributes;

    /** @var array */
    protected $roles;

    /**
     * EzLdapUser constructor.
     */
    public function __construct(string $username, string $email, array $attributes, array $roles)
    {
        $this->username = $username;
        $this->email = $email;
        $this->attributes = $attributes;
        $this->roles = $roles;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * {@inheritDoc}
     */
    public function getPassword(): string
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials(): void
    {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}

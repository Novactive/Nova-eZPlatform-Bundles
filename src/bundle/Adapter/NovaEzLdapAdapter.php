<?php

namespace Novactive\EzLdapAuthenticatorBundle\Adapter;

use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;

class NovaEzLdapAdapter extends Adapter
{
    public function __construct(array $config)
    {
        parent::__construct($config['ldap']['connection']);
    }
}
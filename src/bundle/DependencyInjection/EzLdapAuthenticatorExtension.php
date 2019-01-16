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

namespace Novactive\EzLdapAuthenticatorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class EzLdapAuthenticatorExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $adapterConfig = [
            'host' => $config['host'],
            'port' => $config['port'],
            'version' => $config['version'],
        ];
        $adapterDefinition = $container->getDefinition('Symfony\Component\Ldap\Adapter\ExtLdap\Adapter');
        $adapterDefinition->setArgument(0, $adapterConfig);

        $userProviderArguments = [
            'security.ldap.ldap',
            $config['base_dn'],
            $config['search_dn'],
            $config['search_password'],
            $config['default_roles'],
            $config['uid_key'],
            $config['query_string'],
            $config['password_attribute'],
        ];
        $userProviderDefinition = $container->getDefinition('nova_ez.ldap.user.provider');
        $userProviderDefinition->setArguments($userProviderArguments);

        $loginListener = $container->getDefinition('nova_ez.ldap.login_listener');
        $loginListener->addMethodCall('setConfig', $config);
    }
}

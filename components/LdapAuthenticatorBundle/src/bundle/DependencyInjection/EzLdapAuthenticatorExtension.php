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

namespace Novactive\Bundle\eZLDAPAuthenticatorBundle\DependencyInjection;

use Novactive\eZLDAPAuthenticator\Ldap\LdapConnection;
use Novactive\eZLDAPAuthenticator\User\Converter\LdapEntryConverter;
use Novactive\eZLDAPAuthenticator\User\Provider\EzLdapUserProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

class EzLdapAuthenticatorExtension extends Extension implements PrependExtensionInterface
{
    /** @var string */
    private $defaultConnection;

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (empty($config['default_connection'])) {
            $keys                         = array_keys($config['connections']);
            $config['default_connection'] = reset($keys);
        }
        $this->defaultConnection = $config['default_connection'];

        $connectionId = sprintf('nova_ez.ldap.%s_connection', $this->defaultConnection);
        $container->setAlias('nova_ez.ldap.connection', $connectionId);
        $container->getAlias('nova_ez.ldap.connection')->setPublic(true);

        $connections = [];

        foreach (array_keys($config['connections']) as $name) {
            $connections[$name] = sprintf('nova_ez.ldap.%s_connection', $name);
        }

        $container->setParameter('nova_ez.ldap.connections', $connections);
        $container->setParameter('nova_ez.ldap.default_connection', $this->defaultConnection);

        foreach ($config['connections'] as $name => $connection) {
            $this->loadLdapConnection($name, $connection, $container);
        }
    }

    protected function loadLdapConnection($name, array $connection, ContainerBuilder $container)
    {
        [ 'ldap' => $ldapConfig, 'ezuser' => $ezuserConfig ] = $connection;

        $ldapEntryConverterId = sprintf('nova_ez.ldap.%s_connection.ldap_entry_converter', $name);
        $container->setDefinition($ldapEntryConverterId, new ChildDefinition(LdapEntryConverter::class))
                  ->setArguments(['$options' => $ezuserConfig]);

        $adapterId = sprintf('nova_ez.ldap.%s_connection.adapter', $name);
        $container->setDefinition($adapterId, new ChildDefinition('nova_ez.ldap.default.adapter'))
                  ->setArguments(['$config' => $ldapConfig['adapter']]);

        $ldapId = sprintf('nova_ez.ldap.%s_connection.ldap', $name);
        $container->setDefinition($ldapId, new ChildDefinition('nova_ez.ldap.default'))
                  ->setArguments(['$adapter' => new Reference($adapterId)]);

        $userProviderConfig = $ldapConfig['user_provider'];
        $ldapUserProviderId = sprintf('nova_ez.ldap.%s_connection.ldap_user_provider', $name);
        $container->setDefinition($ldapUserProviderId, new ChildDefinition(EzLdapUserProvider::class))
                  ->setArguments(
                      [
                          '$ldap'              => new Reference($ldapId),
                          '$baseDn'            => $userProviderConfig['base_dn'],
                          '$searchDn'          => $userProviderConfig['search_dn'],
                          '$searchPassword'    => $userProviderConfig['search_password'],
                          '$uidKey'            => $userProviderConfig['uid_key'],
                          '$filter'            => $userProviderConfig['filter'],
                      ]
                  )
                  ->addMethodCall('setLdapEntryConverter', [new Reference($ldapEntryConverterId)]);

        $connectionId = sprintf('nova_ez.ldap.%s_connection', $name);
        $container->setDefinition($connectionId, new ChildDefinition(LdapConnection::class))
                  ->setPublic(true)
                  ->setArguments(
                      [
                          '$ldap'             => new Reference($ldapId),
                          '$ldapUserProvider' => new Reference($ldapUserProviderId),
                          '$configs'          => [
                              'ez_user'            => $ezuserConfig,
                              'adapter'            => $ldapConfig['adapter'],
                              'ldap_user_provider' => $ldapConfig['user_provider'],
                              'ldap_auth'          => [
                                  'dn_string'    => $ldapConfig['user_provider']['base_dn'],
                                  'query_string' => str_replace(
                                      '{uid_key}',
                                      $userProviderConfig['uid_key'],
                                      $userProviderConfig['filter']
                                  ),
                              ],
                          ],
                      ]
                  );
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        $this->prependSecurity($container);
    }

    private function prependSecurity(ContainerBuilder $container): void
    {
        $configFile = __DIR__.'/../Resources/config/security.yml';
        $config     = Yaml::parse((string) file_get_contents($configFile));
        $container->prependExtensionConfig('security', $config);
        $container->addResource(new FileResource($configFile));
    }

    public function getAlias(): string
    {
        return 'nova_ez_ldap';
    }
}

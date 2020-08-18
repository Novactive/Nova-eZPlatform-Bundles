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

namespace Novactive\eZLDAPAuthenticator\User\Converter;

use Exception;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\User\User as EzApiUser;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Novactive\eZLDAPAuthenticator\User\EzLdapUser;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LdapEntryConverter
{
    public const EMAIL_ATTR_OPTION    = 'email_attr';
    public const ATTRIBUTES_OPTION    = 'attributes';
    public const ADMIN_USER_ID_OPTION = 'admin_user_id';
    public const USER_GROUP_ID_OPTION = 'user_group_id';

    /** @var Repository */
    protected $repository;

    /** @var ConfigResolverInterface */
    protected $configResolver;

    /**
     * @var array
     */
    protected $options;

    /**
     * LdapEntryConverter constructor.
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    public function setOptions(array $options): void
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                self::ATTRIBUTES_OPTION => [],
            ]
        );
        $resolver->setRequired(self::EMAIL_ATTR_OPTION);
        $resolver->setRequired(self::ADMIN_USER_ID_OPTION);
        $resolver->setRequired(self::USER_GROUP_ID_OPTION);
        $resolver->setAllowedTypes(self::EMAIL_ATTR_OPTION, 'string');
        $resolver->setAllowedTypes(self::ATTRIBUTES_OPTION, 'array');
        $resolver->setAllowedTypes(self::ADMIN_USER_ID_OPTION, 'int');
        $resolver->setAllowedTypes(self::USER_GROUP_ID_OPTION, 'int');
    }

    /**
     * @required
     */
    public function setRepository(Repository $repository): void
    {
        $this->repository = $repository;
    }

    /**
     * @required
     */
    public function setConfigResolver(ConfigResolverInterface $configResolver): void
    {
        $this->configResolver = $configResolver;
    }

    /**
     * @throws Exception
     */
    public function convert(string $username, Entry $entry): EzLdapUser
    {
        $attributes    = [];
        $attributesMap = $this->options[self::ATTRIBUTES_OPTION];
        foreach ($attributesMap as $attributeIdentifier => $attributeValueIdentifier) {
            $attributes[$attributeIdentifier] = $this->getEntryAttribute($entry, $attributeValueIdentifier);
        }
        $email = (string) $this->getEntryAttribute($entry, $this->options[self::EMAIL_ATTR_OPTION]);

        return new EzLdapUser($username, $email, $attributes, ['ROLE_USER']);
    }

    /**
     * @return array|mixed|null
     */
    protected function getEntryAttribute(Entry $entry, string $attributeName)
    {
        $attributeValue = $entry->getAttribute($attributeName);
        if (is_array($attributeValue) && 1 === count($attributeValue)) {
            return reset($attributeValue);
        }

        return $attributeValue;
    }

    /**
     * @throws Exception
     */
    public function convertToEzUser(string $username, string $email, array $attributes): EzApiUser
    {
        $userService = $this->repository->getUserService();

        try {
            $eZUser = $userService->loadUserByLogin($username);
        } catch (NotFoundException $exception) {
            $languages          = $this->configResolver->getParameter('languages');
            $mainLanguage       = array_shift($languages);
            $eZUserCreateStruct = $userService->newUserCreateStruct(
                $username,
                $email,
                md5(uniqid(EzLdapUser::class, true)),
                $mainLanguage
            );

            foreach ($attributes as $attributeIdentifier => $attributeValue) {
                $eZUserCreateStruct->setField($attributeIdentifier, $attributeValue);
            }
            $eZUserCreateStruct->enabled = true;
            $eZUserCreateStruct->ownerId = $this->options[self::ADMIN_USER_ID_OPTION];

            // Create new user under 'admin' user
            $eZUser = $this->repository->sudo(
                function (Repository $repository) use ($eZUserCreateStruct) {
                    $userService = $repository->getUserService();

                    $userGroup = $userService->loadUserGroup(
                        $this->options[self::USER_GROUP_ID_OPTION]
                    );

                    return $userService->createUser($eZUserCreateStruct, [$userGroup]);
                }
            );
        }

        return $eZUser;
    }
}

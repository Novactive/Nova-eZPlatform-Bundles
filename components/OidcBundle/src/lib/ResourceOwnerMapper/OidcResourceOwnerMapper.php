<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaOidc\ResourceOwnerMapper;

use AlmaviaCX\Bundle\IbexaOidc\Client\Provider\OidcGenericResourceOwner;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\User as APIUser;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\OAuth2Client\Repository\OAuth2UserService;
use Ibexa\Core\MVC\Symfony\Security\User;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\OAuth2Client\ResourceOwner\ResourceOwnerToExistingOrNewUserMapper;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class OidcResourceOwnerMapper extends ResourceOwnerToExistingOrNewUserMapper
{
    public const LOAD_METHOD_LOGIN = 'loadUserByLogin';
    public const LOAD_METHOD_EMAIL = 'loadUserByEmail';

    public const PROVIDER_PREFIX = 'oidc:';

    public function __construct(
        Repository $repository,
        protected OAuth2UserService $oauthUserService,
        protected LanguageResolver $languageResolver,
        protected UserService $userService,
        protected ConfigResolverInterface $configResolver,
    ) {
        parent::__construct($repository);
    }

    /**
     * @param OidcGenericResourceOwner $resourceOwner
     */
    protected function loadUser(
        ResourceOwnerInterface $resourceOwner,
        UserProviderInterface $userProvider,
    ): ?UserInterface {
        try {
            $apiUser = $this->loadApiUser($resourceOwner);

            return $this->createSecurityUser($apiUser);
        } catch (NotFoundException $e) {
            return $userProvider->loadUserByUsername($resourceOwner->getUsername());
        }
    }

    /**
     * Creates user object, usable by Symfony Security component, from a user object returned by Public API.
     */
    protected function createSecurityUser(APIUser $apiUser): User
    {
        return new User($apiUser, ['ROLE_USER']);
    }

    /**
     * @param OidcGenericResourceOwner $resourceOwner
     *
     * @throws NotFoundException
     */
    protected function loadApiUser(ResourceOwnerInterface $resourceOwner): APIUser
    {
        $loadMethod = $this->configResolver->getParameter('user_load_method', 'almaviacx.oidc.config');
        if ($loadMethod === static::LOAD_METHOD_EMAIL) {
            return $this->userService->loadUserByEmail(
                $resourceOwner->getEmail()
            );
        }

        return $this->userService->loadUserByLogin(
            $resourceOwner->getUsername()
        );
    }

    /**
     * @param OidcGenericResourceOwner $resourceOwner
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function createUser(
        ResourceOwnerInterface $resourceOwner,
        UserProviderInterface $userProvider,
    ): ?UserInterface {
        $userCreateStruct = $this->oauthUserService->newOAuth2UserCreateStruct(
            $resourceOwner->getUsername(),
            $resourceOwner->getEmail(),
            $this->getMainLanguageCode(),
            $this->getOAuth2UserContentType($this->repository)
        );

        $userAttributes = $this->getUserAttributes($resourceOwner);
        foreach ($userAttributes as $fieldDefIdentifier => $value) {
            $userCreateStruct->setField($fieldDefIdentifier, $value);
        }

        $parentGroups = [
            $this->getUserGroup(),
        ];

        $apiUser = $this->userService->createUser($userCreateStruct, $parentGroups);

        return $this->createSecurityUser($apiUser);
    }

    /**
     * @return array<string, mixed>
     */
    private function getUserAttributes(OidcGenericResourceOwner $resourceOwner): array
    {
        $attributesMap = $this->configResolver->getParameter('user_attributes_mapping', 'almaviacx.oidc.config');

        $attributes = [];
        foreach ($attributesMap as $target => $source) {
            $attributes[$target] = $resourceOwner->getAttributeValue($source);
        }

        return $attributes;
    }

    private function getUserGroup(): UserGroup
    {
        $userGroupId = $this->configResolver->getParameter('user_group_id', 'almaviacx.oidc.config');
        if (is_numeric($userGroupId)) {
            return $this->userService->loadUserGroup((int) $userGroupId);
        }

        return $this->userService->loadUserGroupByRemoteId($userGroupId);
    }

    private function getOAuth2UserContentType(Repository $repository): ?ContentType
    {
        $userContentTypeIdentifier = $this->configResolver->getParameter(
            'user_content_type_identifier',
            'almaviacx.oidc.config'
        );
        if (null !== $userContentTypeIdentifier) {
            $contentTypeService = $repository->getContentTypeService();

            return $contentTypeService->loadContentTypeByIdentifier(
                $userContentTypeIdentifier
            );
        }

        return null;
    }

    private function getMainLanguageCode(): string
    {
        // Get first prioritized language for current scope
        return $this->languageResolver->getPrioritizedLanguages()[0];
    }
}

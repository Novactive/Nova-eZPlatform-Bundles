<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaSaml\Security\Saml;

use Hslavich\OneloginSamlBundle\Security\Authentication\Token\SamlToken;
use Hslavich\OneloginSamlBundle\Security\Authentication\Token\SamlTokenInterface;
use Hslavich\OneloginSamlBundle\Security\User\SamlUserFactoryInterface;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\Base\Exceptions\ContentFieldValidationException;
use Ibexa\Core\MVC\Symfony\Security\User;
use Ibexa\Core\Repository\SiteAccessAware\Repository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Throwable;

class SamlUserFactory implements SamlUserFactoryInterface
{
    protected ConfigResolverInterface $configResolver;
    protected Repository $repository;
    protected SamlExceptionLogger $logger;
    protected TranslatableNotificationHandlerInterface $notificationHandler;
    protected string $emailAttribute;

    /**
     * @param \AlmaviaCX\Bundle\IbexaSaml\Security\Saml\SamlExceptionLogger $logger
     */
    public function __construct(
        ConfigResolverInterface $configResolver,
        Repository $repository,
        SamlExceptionLogger $logger,
        TranslatableNotificationHandlerInterface $notificationHandler,
        string $emailAttribute
    ) {
        $this->configResolver = $configResolver;
        $this->repository = $repository;
        $this->logger = $logger;
        $this->notificationHandler = $notificationHandler;
        $this->emailAttribute = $emailAttribute;
    }

    /**
     * @param SamlToken $username
     */
    public function createUser($username, array $attributes = []): UserInterface
    {
        if ($username instanceof SamlTokenInterface) {
            trigger_deprecation(
                'hslavich/oneloginsaml-bundle',
                '2.1',
                'Usage of %s is deprecated.',
                SamlTokenInterface::class
            );

            [ $username, $attributes ] = [$username->getUserIdentifier(), $username->getAttributes()];
        }

        return $this->repository->sudo(
            function (Repository $repository) use ($username, $attributes) {
                try {
                    $userService = $this->repository->getUserService();

                    $userGroupId = $this->configResolver->getParameter('user_group_id', 'almaviacx.saml.config');
                    if (is_numeric($userGroupId)) {
                        $userGroup = $userService->loadUserGroup((int) $userGroupId);
                    } else {
                        $userGroup = $userService->loadUserGroupByRemoteId($userGroupId);
                    }

                    $mainLanguageCode = $this->getMainLanguage();
                    $emailAddress = $this->getAttributeValue($attributes, $this->emailAttribute);
                    $newUserCreateStruct = $userService->newUserCreateStruct(
                        $username,
                        $emailAddress,
                        $this->generateRandomPassword(),
                        $mainLanguageCode
                    );

                    $attributesMapping = $this->configResolver->getParameter(
                        'user_attributes_mapping',
                        'almaviacx.saml.config'
                    );
                    foreach ($attributesMapping as $fieldIdentifier => $attributeName) {
                        $newUserCreateStruct
                            ->setField(
                                $fieldIdentifier,
                                $attributeName ? $this->getAttributeValue($attributes, $attributeName) : null,
                                $mainLanguageCode
                            );
                    }
                    $apiUser = $userService->createUser($newUserCreateStruct, [$userGroup]);
                    $repository->getPermissionResolver()->setCurrentUserReference($apiUser);
                    $this->logger->logInfo(
                        sprintf(
                            'An SSO user with data "%s" was created successfully in BO user Group,'.
                            'UserContentId:"%s" GroupUserId:"%s" ',
                            json_encode($attributes, JSON_THROW_ON_ERROR),
                            $apiUser->contentInfo->id,
                            $userGroup->contentInfo->id
                        )
                    );
                    $this->notificationHandler->success(
                        sprintf(
                            'Votre Compte BO avec Login: "%s" et Email: "%s " est bien créé dans le Group "%s".',
                            $username,
                            $emailAddress,
                            $userGroup->contentInfo->name
                        )
                    );

                    return new User($apiUser, ['ROLE_USER']);
                } catch (ContentFieldValidationException $exception) {
                    $newException = ContentFieldValidationException::createNewWithMultiline(
                        $exception->getFieldErrors()
                    );
                    $this->logger->logException($newException);
                    $this->notificationHandler->error($newException->getMessage());
                } catch (Throwable $exception) {
                    $this->logger->logException($exception);
                    $this->notificationHandler->error($exception->getMessage());
                }

                $ex = new UserNotFoundException(sprintf('There is no user with identifier "%s".', $username));
                $ex->setUserIdentifier($username);
                throw $ex;
            }
        );
    }

    protected function getAttributeValue(array $attributes, string $name): ?string
    {
        $attributeValue = $attributes[$name] ?? null;

        return is_array($attributeValue) ? reset($attributeValue) : $attributeValue;
    }

    protected function generateRandomPassword(): string
    {
        return base64_encode(bin2hex(random_bytes(6)));
    }

    protected function getMainLanguage(): string
    {
        $languages = $this->configResolver->getParameter('languages');

        return reset($languages);
    }
}

<?php
declare( strict_types=1 );

namespace AlmaviaCX\Bundle\IbexaSaml\Security\Saml;

use Hslavich\OneloginSamlBundle\Security\Authentication\Token\SamlToken;
use Hslavich\OneloginSamlBundle\Security\User\SamlUserFactoryInterface;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Routing\Generator\UrlAliasGenerator;
use Ibexa\Core\MVC\Symfony\Security\User;
use Ibexa\Core\MVC\Symfony\SiteAccess\Router;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\Repository\SiteAccessAware\Repository;
use Symfony\Component\Security\Core\User\UserInterface;
use Throwable;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;

class SamlUserFactory implements SamlUserFactoryInterface
{
    protected ConfigResolverInterface $configResolver;
    protected Repository $repository;
    protected UrlAliasGenerator $urlAliasGenerator;
    protected SiteAccessServiceInterface $siteAccessService;
    protected SamlExceptionLogger $ssoExceptionLogger;
    protected Router $siteAccessRouter;
    protected TranslatableNotificationHandlerInterface $notificationHandler;

    public function __construct(
        ConfigResolverInterface                  $configResolver,
        Repository                               $repository,
        SiteAccessServiceInterface               $siteAccessService,
        UrlAliasGenerator                        $urlAliasGenerator,
        Router                                   $siteAccessRouter,
        SamlExceptionLogger                      $ssoExceptionLogger,
        TranslatableNotificationHandlerInterface $notificationHandler,
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->siteAccessRouter = $siteAccessRouter;
        $this->ssoExceptionLogger = $ssoExceptionLogger;
        $this->siteAccessService = $siteAccessService;
        $this->urlAliasGenerator = $urlAliasGenerator;
        $this->repository = $repository;
        $this->configResolver = $configResolver;
    }

    /**
     * @param SamlToken $username
     * @param array $attributes
     * @return UserInterface
     */
    public function createUser($username, array $attributes = []): UserInterface
    {
    dd($username);
        return $this->repository->sudo(
            function(Repository $repository) {

                $userService = $this->repository->getUserService();

                $userGroupId = $this->configResolver->getParameter('user_group_id', 'almaviacx.saml.config');
                if(is_string($userGroupId)) {
                    $userGroup = $userService->loadUserGroupByRemoteId($userGroupId);
                }else {
                    $userGroup = $userService->loadUserGroup($userGroupId);
                }

                $newUserCreateStruct = $userService->newUserCreateStruct(
                    $username->getUserIdentifier(),
                    $samlFriendlyNameAttributes['email_address'],
                    $this->passwordGenerator->generate(),
                    $mainLanguageCode
                );





            }
        );

//
//
//
//        $samlUserAttributes = $username->getAttributes();
//
//        $boUserGroupRemoteId = $this->configResolver
//            ->getParameter('bo_user_group_remote_id', 'app');
//        $samlFriendlyNameAttributes = $this->configResolver
//            ->getParameter('saml_friendly_name_attributes', 'app');
//        $samlFriendlyNameAttributes = array_map(static function ($value) use ($samlUserAttributes) {
//            return $samlUserAttributes[$value][0] ?? null;
//        }, $samlFriendlyNameAttributes);
//
//        return $this->repository->sudo(
//            function (Repository $repo) use ($username, $boUserGroupRemoteId, $samlFriendlyNameAttributes) {
//                try {
//                    $userAdmin = $repo->getUserService()->loadUserByLogin('admin');
//                    $repo->getPermissionResolver()->setCurrentUserReference($userAdmin);
//                    // Create a new user that belongs to Bo user group
//                    $mainLanguageCode = 'fre-FR';
//                    $newUserCreateStruct = $repo->getUserService()->newUserCreateStruct(
//                        $username->getUserIdentifier(),
//                        $samlFriendlyNameAttributes['email_address'],
//                        $this->passwordGenerator->generate(),
//                        $mainLanguageCode
//                    );
//                    $newUserCreateStruct
//                        ->setField('first_name', $samlFriendlyNameAttributes['first_name'], $mainLanguageCode);
//                    $newUserCreateStruct
//                        ->setField('last_name', $samlFriendlyNameAttributes['last_name'], $mainLanguageCode);
//                    // load parent group for the user
//                    $group = $repo->getUserService()->loadUserGroupByRemoteId($boUserGroupRemoteId);
//                    $newUserCreateStruct->enabled = true;
//                    // create a new user instance.
//                    $apiUser = $repo->getUserService()->createUser($newUserCreateStruct, [$group]);
//                    $repo->getPermissionResolver()->setCurrentUserReference($apiUser);
//                    $this->ssoExceptionLogger->logInfo(sprintf(
//                        'An SSO user with data "%s" was created successfully in BO user Group,'.
//                        'UserContentId:"%s" GroupUserId:"%s" ',
//                        json_encode($samlFriendlyNameAttributes, JSON_THROW_ON_ERROR),
//                        $apiUser->contentInfo->id,
//                        $group->contentInfo->id
//                    ));
//                    $this->notificationHandler->success(sprintf(
//                        'Votre Compte BO avec Login: "%s" et Email: "%s " est bien créé dans le Group "%s".',
//                        $username->getUserIdentifier(),
//                        $samlFriendlyNameAttributes['email_address'],
//                        $group->contentInfo->name
//                    ));
//                    return new User($apiUser, ['ROLE_USER']);
//                } catch (Throwable $exception) {
//                    $this->ssoExceptionLogger->logException($exception);
//                    $this->notificationHandler->error($exception->getMessage());
//                }
//            }
//        );
    }
}

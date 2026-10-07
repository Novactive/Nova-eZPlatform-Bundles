<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaSiteFactoryExtra\MessageHandler;

use AlmaviaCX\Bundle\IbexaSiteFactoryExtra\Message\CreateUserGroup;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SubtreeLimitation;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateUserGroupMessageHandler extends AbstractSiteFactoryMessageHandler
{
    use ContentHandlerTrait;

    public function __invoke(CreateUserGroup $message)
    {
        $site = $this->siteService->loadSite($message->siteId);

        $this->repository->sudo(function (Repository $repository) use ($site, $message) {
            $usersRoot = $repository->getLocationService()->loadLocationByRemoteId('minisites_users_root');
            $userGroupContent = $this->createContent(
                'user_group',
                [$usersRoot],
                [
                    'fre-FR' => [
                        'name' => $site->name,
                    ],
                ],
                sprintf(
                    'site_%s_users_group',
                    $site->id
                ),
                $message->creatorId,
                'fre-FR'
            );

            $rootLocation = $repository->getLocationService()->loadLocation($site->getTreeRootLocationId());
            $userGroup = $repository->getUserService()->loadUserGroup($userGroupContent->id);

            $role = $repository->getRoleService()->loadRoleByIdentifier('Contributeur minisite');
            $roleLimitation = new SubtreeLimitation(
                [
                    'limitationValues' => [$rootLocation->getPathString()],
                ]
            );

            $repository->getRoleService()->assignRoleToUserGroup(
                $role,
                $userGroup,
                $roleLimitation
            );
        });
    }
}

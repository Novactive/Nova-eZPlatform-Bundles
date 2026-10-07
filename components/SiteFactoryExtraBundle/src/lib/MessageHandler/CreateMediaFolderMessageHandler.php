<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaSiteFactoryExtra\MessageHandler;

use AlmaviaCX\Bundle\IbexaSiteFactoryExtra\Message\CreateMediaFolder;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SubtreeLimitation;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateMediaFolderMessageHandler extends AbstractSiteFactoryMessageHandler
{
    use ContentHandlerTrait;

    public function __invoke(CreateMediaFolder $message)
    {
        $site = $this->siteService->loadSite($message->siteId);

        $this->repository->sudo(function (Repository $repository) use ($site, $message) {
            $mediasRoot = $repository->getLocationService()->loadLocationByRemoteId('minisites_medias_root');

            $mediasFolderContent = $this->createContent(
                'folder',
                [$mediasRoot],
                [
                    'fre-FR' => [
                        'name' => $site->name,
                    ],
                ],
                sprintf(
                    'site_%s_medias',
                    $site->id
                ),
                $message->creatorId,
                'fre-FR',
                alwaysAvailable: true
            );

            $userGroup = $repository->getUserService()->loadUserGroupByRemoteId(
                sprintf(
                    'site_%s_users_group',
                    $site->id
                )
            );

            $role = $repository->getRoleService()->loadRoleByIdentifier('Contributeur minisite');
            $roleLimitation = new SubtreeLimitation(
                [
                    'limitationValues' => [$mediasFolderContent->getContentInfo()->getMainLocation()->getPathString()],
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

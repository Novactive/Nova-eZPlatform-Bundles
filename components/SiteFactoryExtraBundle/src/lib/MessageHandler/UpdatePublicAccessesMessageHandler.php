<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaSiteFactoryExtra\MessageHandler;

use AlmaviaCX\Bundle\IbexaSiteFactoryExtra\Message\UpdatePublicAccesses;
use Exception;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ParentLocationId;
use Ibexa\Contracts\SiteFactory\Service\PublicAccessServiceInterface;
use Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface;
use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteConfiguration;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteUpdateStruct;
use Ibexa\SiteFactory\Persistence\Site\Handler\HandlerInterface as SiteHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdatePublicAccessesMessageHandler
{
    public function __construct(
        protected SiteServiceInterface $siteService,
        protected PublicAccessServiceInterface $publicAccessService,
        protected Repository $repository,
        protected SiteHandler $siteHandler
    ) {
    }

    public function __invoke(UpdatePublicAccesses $message): void
    {
        $site = $this->siteService->loadSite($message->siteId);

        $treeRootLocationId = $site->getTreeRootLocationId();

        if ($message->isCreate) {
            $this->repository->sudo(function (Repository $repository) use ($site, &$treeRootLocationId) {
                $searchQuery = new Query();
                $searchQuery->filter = new LogicalAnd(
                    [
                        new ParentLocationId($treeRootLocationId),
                    ]
                );
                $searchResults = $repository->getSearchService()->findContent($searchQuery);
                foreach ($searchResults as $searchResult) {
                    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
                    $content = $searchResult->valueObject;
                    if ('minisite_homepage' === $content->getContentType()->identifier) {
                        $treeRootLocationId = $content->getContentInfo()->getMainLocationId();
                        $metadataUpdateStruct = $repository->getContentService()->newContentMetadataUpdateStruct();
                        $metadataUpdateStruct->remoteId = sprintf(
                            'site_%s_homepage',
                            $site->id
                        );
                        $repository->getContentService()->updateContentMetadata(
                            $content->getContentInfo(),
                            $metadataUpdateStruct
                        );
                    } elseif ('minisite_configuration' === $content->getContentType()->identifier) {
                        $metadataUpdateStruct = $repository->getContentService()->newContentMetadataUpdateStruct();
                        $metadataUpdateStruct->remoteId = sprintf(
                            'site_%s_configuration',
                            $site->id
                        );
                        $repository->getContentService()->updateContentMetadata(
                            $content->getContentInfo(),
                            $metadataUpdateStruct
                        );
                    }
                }
            });
        }

        $updatedPublicAccesses = [];
        $siteAccessIdentifiers = array_map(static function (PublicAccess $publicAccess) {
            return $publicAccess->identifier;
        }, $site->publicAccesses);

        foreach ($site->publicAccesses as $publicAccess) {
            $siteAccessConfiguration = $publicAccess->getSiteConfiguration()->getValues();
            $siteAccessConfiguration['ibexa.site_access.config.translation_siteaccesses'] = $siteAccessIdentifiers;
            $siteAccessConfiguration[SiteConfiguration::TREE_ROOT_LOCATION_ID] = $treeRootLocationId;

            $updatedPublicAccesses[] = new PublicAccess(
                $publicAccess->identifier,
                $publicAccess->siteId,
                $publicAccess->saGroup,
                $publicAccess->matcherConfiguration,
                new SiteConfiguration($siteAccessConfiguration),
                $publicAccess->status,
            );
        }

        $siteUpdateStruct = new SiteUpdateStruct(
            $site->name,
            $updatedPublicAccesses
        );

        $this->repository->beginTransaction();
        try {
            $this->siteHandler->update($site->id, $siteUpdateStruct);
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }
}

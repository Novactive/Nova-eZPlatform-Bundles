<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaSiteFactoryExtra\MessageHandler;

use App\Service\ContentImportService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface;

abstract class AbstractSiteFactoryMessageHandler
{
    public function __construct(
        protected SiteServiceInterface $siteService,
        protected LocationService $locationService,
        protected PermissionResolver $permissionResolver,
        protected ContentImportService $contentImportService
    ) {
    }
}

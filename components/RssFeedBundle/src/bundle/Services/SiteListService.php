<?php

namespace Novactive\EzRssFeedBundle\Services;

use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SiteListService implements SiteListServiceInterface
{
    protected SiteAccessServiceInterface $siteAccessService;
    protected TranslatorInterface $translator;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
        $this->siteAccessService = $siteAccessService;
    }

    public function addSiteAccessList(): array
    {
        $sites = [];
        foreach ($this->siteAccessService->getAll() as $siteAccess) {
            $sites[$this->translator->trans($siteAccess->name, [], 'novarss_sites')] = $siteAccess->name;
        }

        return $sites;
    }
}
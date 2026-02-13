<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Taxonomy;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException;

class IbexaTaxonomyImporter
{
    protected Repository $repository;
    protected TaxonomyServiceInterface $taxonomyService;
    protected IbexaTaxonomyCreator $taxonomyCreator;
    protected IbexaTaxonomyUpdater $taxonomyUpdater;

    public function __construct(
        Repository $repository,
        TaxonomyServiceInterface $taxonomyService,
        IbexaTaxonomyCreator $taxonomyCreator,
        IbexaTaxonomyUpdater $taxonomyUpdater,
    ) {
        $this->taxonomyUpdater = $taxonomyUpdater;
        $this->taxonomyCreator = $taxonomyCreator;
        $this->taxonomyService = $taxonomyService;
        $this->repository = $repository;
    }

    public function __invoke(IbexaTaxonomyData $ibexaTaxonomyData, bool $allowUpdate = true): TaxonomyEntry
    {
        $remoteId = $ibexaTaxonomyData->getContentRemoteId();
        $ownerId = $ibexaTaxonomyData->getOwnerId();
        if (null === $ownerId) {
            $ownerId = $this->repository
                ->getPermissionResolver()
                ->getCurrentUserReference()
                ->getUserId();
        }

        try {
            $parent = $this->taxonomyService->loadEntryByIdentifier(
                $ibexaTaxonomyData->getParentIdentifier(),
                $ibexaTaxonomyData->getTaxonomyName()
            );
            try {
                $taxonomyEntry = $this->taxonomyService->loadEntryByIdentifier(
                    $ibexaTaxonomyData->getIdentifier(),
                    $ibexaTaxonomyData->getTaxonomyName()
                );
                if (!$allowUpdate) {
                    return $taxonomyEntry;
                }

                return ($this->taxonomyUpdater)(
                    $taxonomyEntry,
                    $parent,
                    $ibexaTaxonomyData->getNames(),
                    $ownerId,
                    $ibexaTaxonomyData->getMainLanguageCode()
                );
            } catch (TaxonomyEntryNotFoundException $exception) {
                return ($this->taxonomyCreator)(
                    $ibexaTaxonomyData->getIdentifier(),
                    $parent,
                    $ibexaTaxonomyData->getNames(),
                    $remoteId,
                    $ownerId,
                    $ibexaTaxonomyData->getMainLanguageCode(),
                    $ibexaTaxonomyData->getSectionId(),
                    $ibexaTaxonomyData->getModificationDate()
                );
            }
        } catch (\Throwable $exception) {
            dump($exception, $ibexaTaxonomyData);
            throw $exception;
        }
    }
}

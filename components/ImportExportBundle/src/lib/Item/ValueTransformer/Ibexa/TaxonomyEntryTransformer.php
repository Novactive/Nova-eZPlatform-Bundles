<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Ibexa;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use Exception;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxonomyEntryTransformer extends AbstractItemValueTransformer
{
    protected TaxonomyServiceInterface $taxonomyService;

    public function __construct(TaxonomyServiceInterface $taxonomyService)
    {
        $this->taxonomyService = $taxonomyService;
    }

    /**
     * @param int|string|array<string|int> $value
     *
     * @return TaxonomyEntry|TaxonomyEntry[]|null
     */
    public function transform($value, array $options = [])
    {
        if (empty($value)) {
            return null;
        }
        $taxonomy = $options['taxonomy'] ?? null;

        if (is_scalar($value)) {
            return $this->loadTaxonomyEntry($value, $taxonomy);
        }

        $entries = [];
        foreach ($value as $id) {
            $entries[] = $this->loadTaxonomyEntry($id, $taxonomy);
        }

        return array_filter($entries);
    }

    /**
     * @param int|string $id
     */
    protected function loadTaxonomyEntry($id, ?string $taxonomyName = null): ?TaxonomyEntry
    {
        try {
            if (is_string($id)) {
                return $this->taxonomyService->loadEntryByIdentifier($id, $taxonomyName);
            }

            return $this->taxonomyService->loadEntryById($id, $taxonomyName);
        } catch (TaxonomyEntryNotFoundException $exception) {
            throw new Exception(sprintf('No taxonomy entry found for id/identifier "%s"', $id));
        }
    }

    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        parent::configureOptions($optionsResolver);
        $optionsResolver->define('taxonomy')
            ->required()
            ->allowedTypes('string');
    }
}

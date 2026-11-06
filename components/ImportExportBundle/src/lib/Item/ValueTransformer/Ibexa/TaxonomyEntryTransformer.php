<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Ibexa;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use Exception;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Transform a string or array of strings to a TaxonomyEntry or an array of TaxonomyEntry.
 * Accept a 'taxonomy' option to specify the taxonomy name.
 */
class TaxonomyEntryTransformer extends AbstractItemValueTransformer
{
    public function __construct(
        protected TaxonomyServiceInterface $taxonomyService
    ) {
    }

    /**
     * @param int|string|array<string|int> $value
     *
     * @return TaxonomyEntry|TaxonomyEntry[]|null
     */
    protected function transform(mixed $value, array $options = [])
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
            if (empty($id)) {
                continue;
            }
            $entries[] = $this->loadTaxonomyEntry($id, $taxonomy);
        }

        return array_filter($entries);
    }

    protected function loadTaxonomyEntry(int|string $id, ?string $taxonomyName = null): ?TaxonomyEntry
    {
        try {
            if (is_string($id)) {
                return $this->taxonomyService->loadEntryByIdentifier($id, $taxonomyName);
            }

            return $this->taxonomyService->loadEntryById($id);
        } catch (TaxonomyEntryNotFoundException $exception) {
            throw new Exception(sprintf('No taxonomy entry found for id/identifier "%s" in "%s"', $id, $taxonomyName));
        }
    }

    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
        parent::configureOptions($optionsResolver);
        $optionsResolver->define('taxonomy')
            ->required()
            ->allowedTypes('string');
    }
}

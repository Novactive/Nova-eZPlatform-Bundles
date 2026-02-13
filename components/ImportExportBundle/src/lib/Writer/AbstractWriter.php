<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ItemAccessorInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\ItemTransformer;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\SourceResolver;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\AbstractProcessor;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowState;

/**
 * @phpstan-import-type ProcessableItem from \AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface
 * @phpstan-import-type MappedItem from ItemTransformer
 * @phpstan-import-type SourceObjectOrArray from ItemTransformer
 *
 * @template TWriterOptions of WriterOptions
 * @extends  AbstractProcessor<TWriterOptions>
 * @implements WriterInterface<TWriterOptions>
 */
abstract class AbstractWriter extends AbstractProcessor implements WriterInterface
{
    protected WriterResults $results;

    public function __construct(
        protected SourceResolver $sourceResolver,
        protected ItemTransformer $itemTransformer,
    ) {
        $this->results = new WriterResults(static::class, []);
    }

    public static function getOptionsType(): string
    {
        return WriterOptions::class;
    }

    public function setState(WorkflowState $state): void
    {
        parent::setState($state);
        if (!$state->hasWriterResults($this->identifier)) {
            $state->setWriterResults($this->identifier, $this->results);
        } else {
            $this->results = $state->getWriterResults($this->identifier);
        }
    }

    /**
     * @throws \AlmaviaCX\Bundle\IbexaImportExport\Exception\SourceResolutionException
     */
    public function processItem($item): mixed
    {
        $writtenItem = $this->writeItem($item, $this->mapItem($item));
        if (!empty($writtenItem)) {
            $this->setReferences($writtenItem);
        }

        return null;
    }

    /**
     * @param SourceObjectOrArray $objectOrArray
     *
     * @throws \AlmaviaCX\Bundle\IbexaImportExport\Exception\SourceResolutionException
     */
    protected function setReferences($objectOrArray): void
    {
        /** @var WriterOptions $options */
        $options = $this->getOptions();
        if (null === $options->referencesMap) {
            return;
        }
        foreach ($options->referencesMap->getElements() as $referenceName => $referenceSource) {
            $value = ($this->sourceResolver)(
                $referenceSource,
                $objectOrArray,
                $this->getReferenceBag()
            );
            $this->getReferenceBag()->addReference(
                $referenceName,
                $value,
                $referenceSource->getScope(),
                $referenceSource->getConflictResolution()
            );
        }
    }

    /**
     * @param ProcessableItem $item
     * @param MappedItem      $mappedItem
     *
     * @return MappedItem|ItemAccessorInterface|false|null
     */
    abstract protected function writeItem($item, $mappedItem);

    /**
     * @param ProcessableItem $item
     *
     * @throws \AlmaviaCX\Bundle\IbexaImportExport\Exception\SourceResolutionException
     *
     * @return MappedItem
     */
    protected function mapItem($item)
    {
        /** @var \AlmaviaCX\Bundle\IbexaImportExport\Writer\WriterOptions $options */
        $options = $this->getOptions();

        return ($this->itemTransformer)(
            $item,
            $options->map,
            $this->getMappedItemInstance(),
            $this->getReferenceBag()
        );
    }

    /**
     * @return MappedItem
     */
    protected function getMappedItemInstance()
    {
        return [];
    }
}

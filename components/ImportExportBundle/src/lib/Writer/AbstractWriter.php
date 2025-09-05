<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ItemAccessorInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\ItemTransformer;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\SourceResolver;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\AbstractProcessor;
use AlmaviaCX\Bundle\IbexaImportExport\Reference\ReferenceBag;

abstract class AbstractWriter extends AbstractProcessor implements WriterInterface
{
    protected ItemTransformer $itemTransformer;
    protected ReferenceBag $referenceBag;
    protected SourceResolver $sourceResolver;
    protected WriterResults $results;

    public function __construct(
        SourceResolver $sourceResolver,
        ItemTransformer $itemTransformer,
        ReferenceBag $references
    ) {
        $this->sourceResolver = $sourceResolver;
        $this->referenceBag = $references;
        $this->itemTransformer = $itemTransformer;
        $this->results = new WriterResults(static::class, []);
    }

    public static function getOptionsType(): ?string
    {
        return WriterOptions::class;
    }

    public function setResults(WriterResults $results): void
    {
        $this->results = $results;
    }

    public function getResults(): WriterResults
    {
        return $this->results;
    }

    /**
     * @param object|array $item
     *
     * @return ItemAccessorInterface|false|null
     */
    public function processItem($item)
    {
        $writenItem = $this->writeItem($item, $this->mapItem($item));
        $this->setReferences($writenItem);
    }

    protected function setReferences($objectOrArray): void
    {
        /** @var WriterOptions $options */
        $options = $this->getOptions();
        if (null === $options->referencesMap) {
            return;
        }
        foreach ($options->referencesMap->getElements() as $referenceName => $referenceSource) {
            $value = ($this->sourceResolver)($referenceSource, $objectOrArray);
            $this->referenceBag->addReference($referenceName, $value, $referenceSource->getScope());
        }
    }

    /**
     * @param object|array                    $item
     * @param array<int|string, mixed>|object $mappedItem
     *
     * @return false|ItemAccessorInterface|null
     */
    abstract protected function writeItem($item, $mappedItem);

    /**
     * @param object|array $item
     *
     * @return array<int|string, mixed>|object
     */
    protected function mapItem($item)
    {
        /** @var WriterOptions $options */
        $options = $this->getOptions();

        return ($this->itemTransformer)($item, $options->map, $this->getMappedItemInstance());
    }

    protected function getMappedItemInstance()
    {
        return [];
    }
}

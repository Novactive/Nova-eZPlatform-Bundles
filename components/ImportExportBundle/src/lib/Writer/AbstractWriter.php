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

    public function __construct(
        SourceResolver $sourceResolver,
        ItemTransformer $itemTransformer,
        ReferenceBag $references
    ) {
        $this->sourceResolver = $sourceResolver;
        $this->referenceBag = $references;
        $this->itemTransformer = $itemTransformer;
    }

    public static function getOptionsType(): ?string
    {
        return WriterOptions::class;
    }

    public function prepare(): void
    {
    }

    public function finish(): WriterResults
    {
        return new WriterResults(static::class, []);
    }

    /**
     * @param object|array $item
     *
     * @return \AlmaviaCX\Bundle\IbexaImportExport\Item\ItemAccessorInterface|false|null
     */
    public function processItem($item)
    {
        $writenItem = $this->writeItem($item, $this->mapItem($item));
        $this->setReferences($writenItem);
    }

    protected function setReferences($objectOrArray): void
    {
        /** @var \AlmaviaCX\Bundle\IbexaImportExport\Writer\WriterOptions $options */
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
        /** @var \AlmaviaCX\Bundle\IbexaImportExport\Writer\WriterOptions $options */
        $options = $this->getOptions();

        return ($this->itemTransformer)($item, $options->map, $this->getMappedItemInstance());
    }

    protected function getMappedItemInstance()
    {
        return [];
    }
}

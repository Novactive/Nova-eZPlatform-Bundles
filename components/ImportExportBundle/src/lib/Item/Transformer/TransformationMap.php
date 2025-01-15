<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer;

/**
 * @template Source of string|\AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\Source
 */
class TransformationMap
{
    /** @var array<string, Source> */
    protected array $elements;

    /**
     * @param array<string, Source> $elements
     */
    public function __construct(array $elements)
    {
        $this->setElements($elements);
    }

    /**
     * @return array<string, Source>
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @param array<string, Source> $elements
     */
    public function setElements(array $elements): void
    {
        $this->elements = $elements;
    }
}

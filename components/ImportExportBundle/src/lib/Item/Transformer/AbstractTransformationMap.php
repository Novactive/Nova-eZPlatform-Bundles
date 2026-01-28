<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer;

/**
 * @template T
 */
class AbstractTransformationMap
{
    /** @var array<string, T> */
    protected array $elements;

    /**
     * @param array<string, T> $elements
     */
    public function __construct(
        array $elements
    ) {
        $this->setElements($elements);
    }

    /**
     * @return array<string, T>
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @param array<string, T> $elements
     */
    public function setElements(array $elements): void
    {
        $this->elements = $elements;
    }
}

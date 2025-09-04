<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\ItemValueTransformerInterface;
use Symfony\Component\PropertyAccess\PropertyPath;

/**
 * @phpstan-import-type TransformerOptions from ItemValueTransformerInterface
 * @phpstan-type TransformerReference string|array{string, TransformerOptions}
 */
class Source
{
    /** @var PropertyPath[]|PropertyPath */
    protected $path;

    /**
     * @param string[]|string        $path
     * @param TransformerReference[] $transformers
     */
    public function __construct(
        $path,
        protected array $transformers = []
    ) {
        if (is_array($path)) {
            $this->path = array_map(function (string $path) {
                return new PropertyPath($path);
            }, $path);
        } else {
            $this->path = new PropertyPath($path);
        }
    }

    /**
     * @return PropertyPath[]|PropertyPath
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return TransformerReference[]
     */
    public function getTransformers(): array
    {
        return $this->transformers;
    }

    public function __toString(): string
    {
        return (string) $this->path;
    }
}

<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\ItemValueTransformerInterface;
use Symfony\Component\PropertyAccess\PropertyPath;

class Source
{
    /** @var PropertyPath[]|PropertyPath */
    protected $path;

    /** @var ItemValueTransformerInterface[] */
    protected array $transformers = [];

    /**
     * @param string[]|string                            $path
     * @param array<string>|array<array{string, string}> $transformers
     */
    public function __construct($path, array $transformers = [])
    {
        if (is_array($path)) {
            $this->path = array_map(function (string $path) {
                return new PropertyPath($path);
            }, $path);
        } else {
            $this->path = new PropertyPath($path);
        }

        $this->transformers = $transformers;
    }

    /**
     * @return PropertyPath[]|PropertyPath
     */
    public function getPath()
    {
        return $this->path;
    }

    public function getTransformers(): array
    {
        return $this->transformers;
    }

    public function __toString(): string
    {
        return (string) $this->path;
    }
}

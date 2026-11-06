<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer;

use AlmaviaCX\Bundle\IbexaImportExport\Reference\ReferenceBag;
use Ibexa\Contracts\Core\Repository\Exceptions\PropertyNotFoundException;
use ReflectionClass;
use ReflectionProperty;

/**
 * @phpstan-type SourceObjectOrArray array<int|string, mixed>|object
 * @phpstan-type MappedItem array<int|string, mixed>|object
 */
class ItemTransformer
{
    public function __construct(
        protected SourceResolver $sourceResolver
    ) {
    }

    /**
     * @param SourceObjectOrArray                                                    $objectOrArray
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\TransformationMap $map
     * @param MappedItem                                                             $destinationObjectOrArray
     *
     * @throws \AlmaviaCX\Bundle\IbexaImportExport\Exception\SourceResolutionException
     *
     * @return MappedItem
     */
    public function __invoke(
        $objectOrArray,
        TransformationMap $map,
        $destinationObjectOrArray,
        ReferenceBag $referenceBag
    ) {
        $elements = $map->getElements();
        foreach ($elements as $destination => $source) {
            $value = ($this->sourceResolver)($source, $objectOrArray, $referenceBag);
            $this->sourceResolver->getDefaultPropertyAccessor()->setValue(
                $destinationObjectOrArray,
                $destination,
                $value
            );
        }

        return $destinationObjectOrArray;
    }

    /**
     * @param object|string $objectOrClass
     *
     * @throws \ReflectionException
     *
     * @return string[]
     */
    public function getAvailableProperties($objectOrClass): array
    {
        return $this->getPropertiesPaths($objectOrClass);
    }

    /**
     * @param object|string $objectOrClass
     *
     * @throws \ReflectionException
     *
     * @return string[]
     */
    protected function getPropertiesPaths($objectOrClass, string $prefix = ''): array
    {
        $reflectionClass = new ReflectionClass($objectOrClass);
        $properties = [];
        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            try {
                $propertyName = $property->getName();

                $properties = array_merge(
                    $properties,
                    $this->getPropertyPaths(
                        $objectOrClass->{$propertyName},
                        sprintf('%s%s', $prefix, $propertyName)
                    )
                );
            } catch (PropertyNotFoundException $exception) {
                continue;
            }
        }

        return $properties;
    }

    /**
     * @param mixed $value
     *
     * @throws \ReflectionException
     *
     * @return string[]
     */
    protected function getPropertyPaths($value, string $propertyName): array
    {
        $paths = [];
        if (is_array($value)) {
            foreach ($value as $index => $item) {
                $itemPropertyPaths = $this->getPropertyPaths(
                    $item,
                    sprintf('%s[%s]', $propertyName, $index)
                );
                if (!empty($itemPropertyPaths)) {
                    $paths = array_merge($paths, $itemPropertyPaths);
                }
            }

            return $paths;
        }

        if (is_object($value)) {
            return array_merge(
                $paths,
                $this->getPropertiesPaths(
                    $value,
                    sprintf('%s.', $propertyName)
                )
            );
        }

        $paths[] = $propertyName;

        return $paths;
    }
}

<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer;

use Ibexa\Contracts\Core\Repository\Exceptions\PropertyNotFoundException;
use ReflectionClass;
use ReflectionProperty;

class ItemTransformer
{
    protected SourceResolver $sourceResolver;

    public function __construct(
        SourceResolver $sourceResolver
    ) {
        $this->sourceResolver = $sourceResolver;
    }

    /**
     * @param object|array                                                           $objectOrArray
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\TransformationMap $map
     * @param array<int|string, mixed>|object                                        $destinationObjectOrArray
     *
     * @return array<int|string, mixed>|object
     */
    public function __invoke(
        $objectOrArray,
        TransformationMap $map,
        $destinationObjectOrArray = []
    ) {
        $elements = $map->getElements();
        foreach ($elements as $destination => $source) {
            $value = ($this->sourceResolver)($source, $objectOrArray);
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
     */
    public function getAvailableProperties($objectOrClass): array
    {
        return $this->getPropertiesPaths($objectOrClass);
    }

    /**
     * @param object|string $objectOrClass
     *
     * @throws \ReflectionException
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

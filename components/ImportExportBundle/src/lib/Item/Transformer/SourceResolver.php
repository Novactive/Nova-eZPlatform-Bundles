<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\XpathPropertyAccessor;
use AlmaviaCX\Bundle\IbexaImportExport\Exception\SourceResolutionException;
use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\ItemValueTransformerRegistry;
use AlmaviaCX\Bundle\IbexaImportExport\Reference\Reference;
use AlmaviaCX\Bundle\IbexaImportExport\Reference\ReferenceBag;
use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;

class SourceResolver
{
    protected PropertyAccessorInterface $defaultPropertyAccessor;
    protected ItemValueTransformerRegistry $itemValueTransformerRegistry;
    protected ReferenceBag $referenceBag;

    public function __construct(ItemValueTransformerRegistry $itemValueTransformerRegistry, ReferenceBag $referenceBag)
    {
        $this->referenceBag = $referenceBag;
        $this->itemValueTransformerRegistry = $itemValueTransformerRegistry;
        $this->defaultPropertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
                                                       ->getPropertyAccessor();
    }

    public function getDefaultPropertyAccessor(): PropertyAccessorInterface
    {
        return $this->defaultPropertyAccessor;
    }

    /**
     * @param object|array $objectOrArray
     *
     * @return array
     */
    public function getPropertyMultipleValue($objectOrArray, PropertyPath $source)
    {
        $wildcardPosition = strpos((string) $source, '[*]');
        $pathBeforeWildCard = substr((string) $source, 0, $wildcardPosition);
        $pathAfterWildCard = substr((string) $source, $wildcardPosition + 3);

        $value = [];
        $array = $this->getPropertyAccessor($objectOrArray)->getValue($objectOrArray, $pathBeforeWildCard);
        foreach ($array as $element) {
            if (empty($pathAfterWildCard)) {
                $value[] = $element;
            } else {
                $value[] = $this->getPropertyValue(
                    $element,
                    new PropertyPath(ltrim($pathAfterWildCard, '.'))
                );
            }
        }

        return $value;
    }

    /**
     * @param object|array $objectOrArray
     */
    public function getPropertyValue($objectOrArray, $source)
    {
        try {
            if ($source instanceof PropertyPath) {
                if (false !== strpos((string) $source, '[*]')) {
                    return $this->getPropertyMultipleValue($objectOrArray, $source);
                }

                $value = $this->getPropertyAccessor($objectOrArray)->getValue($objectOrArray, $source);

                return is_string($value) ? trim($value) : $value;
            }
        } catch (NoSuchIndexException|NoSuchPropertyException  $exception) {
            return null;
        }

        return $source;
    }

    /**
     * @param object|array $objectOrArray
     */
    private function getPropertyAccessor($objectOrArray): PropertyAccessorInterface
    {
        return $objectOrArray instanceof \DOMNode ?
            new XpathPropertyAccessor() :
            $this->defaultPropertyAccessor;
    }

    /**
     * @param object|array $objectOrArray
     */
    protected function getSourceValue($source, $objectOrArray)
    {
        try {
            if ($source instanceof Reference) {
                return $this->referenceBag->getReference($source->getName(), null, $source->getScope());
            }

            if ($source instanceof Source) {
                $sourcePath = $source->getPath();
                if (is_array($sourcePath)) {
                    $value = array_map(function (PropertyPath $path) use ($objectOrArray) {
                        return $this->getPropertyValue($objectOrArray, $path);
                    }, $sourcePath);
                } else {
                    $value = $this->getPropertyValue($objectOrArray, $sourcePath);
                }

                foreach ($source->getTransformers() as $transformerInfos) {
                    if (is_array($transformerInfos)) {
                        [$transformerType, $transformerOptions] = $transformerInfos;
                    } else {
                        $transformerType = $transformerInfos;
                        $transformerOptions = [];
                    }
                    $transformer = $this->itemValueTransformerRegistry->get($transformerType);
                    $value = $transformer($value, $transformerOptions);
                }

                return $value;
            }

            return $this->getPropertyValue($objectOrArray, $source);
        } catch (\Throwable $exception) {
            throw new SourceResolutionException($source, $exception);
        }
    }

    /**
     * @param object|array $objectOrArray
     */
    public function __invoke($source, $objectOrArray)
    {
        if (is_array($source)) {
            $value = [];
            foreach ($source as $sourceKey => $sourceItem) {
                $sourceItemKey = $this->getSourceValue($sourceKey, $objectOrArray);
                $sourceItemValue = $this->getSourceValue($sourceItem, $objectOrArray);
                $value[$sourceItemKey] = $sourceItemValue;
            }
        } else {
            $value = $this->getSourceValue($source, $objectOrArray);
        }

        return $value;
    }
}

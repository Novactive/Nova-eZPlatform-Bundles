<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\XpathPropertyAccessor;
use AlmaviaCX\Bundle\IbexaImportExport\Exception\SourceResolutionException;
use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\ItemValueTransformerRegistry;
use AlmaviaCX\Bundle\IbexaImportExport\Reference\Reference;
use AlmaviaCX\Bundle\IbexaImportExport\Reference\ReferenceBag;
use DOMNode;
use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;
use Throwable;

/**
 * @phpstan-import-type ElementSource from TransformationMap
 * @phpstan-import-type ElementSourceSingle from TransformationMap
 * @phpstan-import-type SourceObjectOrArray from ItemTransformer
 */
class SourceResolver
{
    protected PropertyAccessorInterface $defaultPropertyAccessor;

    public function __construct(
        protected ItemValueTransformerRegistry $itemValueTransformerRegistry
    ) {
        $this->defaultPropertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
                                                       ->getPropertyAccessor();
    }

    public function getDefaultPropertyAccessor(): PropertyAccessorInterface
    {
        return $this->defaultPropertyAccessor;
    }

    /**
     * @param SourceObjectOrArray $objectOrArray
     *
     * @return mixed[]
     */
    public function getPropertyMultipleValue($objectOrArray, PropertyPath $source): array
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
     * @param SourceObjectOrArray $objectOrArray
     * @param mixed|PropertyPath  $source
     *
     * @return mixed
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
     * @param SourceObjectOrArray $objectOrArray
     */
    private function getPropertyAccessor($objectOrArray): PropertyAccessorInterface
    {
        return $objectOrArray instanceof DOMNode ?
            new XpathPropertyAccessor() :
            $this->defaultPropertyAccessor;
    }

    /**
     * @param ElementSourceSingle $source
     * @param SourceObjectOrArray $objectOrArray
     *
     * @throws \AlmaviaCX\Bundle\IbexaImportExport\Exception\SourceResolutionException
     *
     * @return mixed
     */
    protected function getSourceValue($source, $objectOrArray, ?ReferenceBag $referenceBag = null)
    {
        try {
            if ($source instanceof Reference) {
                return $referenceBag->getReference($source->getName(), null, $source->getScope());
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
                        [ $transformerType, $transformerOptions ] = $transformerInfos;
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
        } catch (Throwable $exception) {
            throw new SourceResolutionException((string) $source, $exception);
        }
    }

    /**
     * @param ElementSource       $source
     * @param SourceObjectOrArray $objectOrArray
     *
     * @throws \AlmaviaCX\Bundle\IbexaImportExport\Exception\SourceResolutionException
     *
     * @return mixed
     */
    public function __invoke($source, $objectOrArray, ?ReferenceBag $referenceBag = null)
    {
        if (is_array($source)) {
            $value = [];
            foreach ($source as $sourceKey => $sourceItem) {
                $sourceItemKey = $this->getSourceValue($sourceKey, $objectOrArray, $referenceBag);
                $sourceItemValue = $this->getSourceValue($sourceItem, $objectOrArray, $referenceBag);
                $value[$sourceItemKey] = $sourceItemValue;
            }
        } else {
            $value = $this->getSourceValue($source, $objectOrArray, $referenceBag);
        }

        return $value;
    }
}

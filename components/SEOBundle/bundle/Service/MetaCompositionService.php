<?php

namespace Novactive\Bundle\eZSEOBundle\Service;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Novactive\Bundle\eZSEOBundle\Core\CustomFallbackInterface;
use Novactive\Bundle\eZSEOBundle\Core\FieldType\Metas\Value as MetasFieldValue;
use Novactive\Bundle\eZSEOBundle\Core\Meta;
use Novactive\Bundle\eZSEOBundle\Core\MetaNameSchema;

class MetaCompositionService
{
    protected MetaNameSchema $metaNameSchema;

    protected ConfigResolverInterface $configResolver;

    protected Repository $eZRepository;

    /**
     * CustomFallBack Service.
     *
     * @var CustomFallbackInterface
     */
    protected ?CustomFallbackInterface $customFallBackService = null;

    public function __construct(
        Repository $repository,
        MetaNameSchema $nameSchema,
        ConfigResolverInterface $configResolver
    ) {
        $this->metaNameSchema = $nameSchema;
        $this->eZRepository = $repository;
        $this->configResolver = $configResolver;
    }

    public function setCustomFallbackService(CustomFallbackInterface $service)
    {
        $this->customFallBackService = $service;
    }

    /**
     * @param ContentInfo $contentInfo
     * @param string $metaName
     * @return string
     */
    public function getFallbackedMetaContent(ContentInfo $contentInfo, string $metaName): string
    {
        if ($this->customFallBackService instanceof CustomFallbackInterface) {
            return $this->customFallBackService->getMetaContent($metaName, $contentInfo);
        }

        return '';
    }

    /**
     * @param Field $field
     * @param $content
     * @return array
     * @throws InvalidArgumentType
     */
    public function computeMetasUsingFallback(Field $field, $content): array
    {
        $this->computeMetas($field, $content);
        $metaFieldValues = $field->value;

        return array_map(function(Meta $meta) use ($content) {
            if ($meta->isEmpty())
            {
                $meta->setContent(
                    $this->getFallbackedMetaContent(
                        $content instanceof Content ? $content->contentInfo : $content,
                        $meta->getName()
                    )
                );
            }

            return $meta;
        }, $metaFieldValues->metas);
    }

    /**
     * Compute Metas of the Field thanks to its Content and the Fallback.
     */
    // @param $content: use type Content rather than ContentInfo, the last one is @deprecated
    public function computeMetas(Field $field, $content): void
    {
        $fallback = false;
        $languages = $this->configResolver->getParameter('languages');

        if ($content instanceof ContentInfo) {
            try {
                $content = $this->eZRepository->getContentService()->loadContentByContentInfo($content, $languages);
            } catch (NotFoundException | UnauthorizedException $e) {
                return;
            }
        } elseif (!($content instanceof Content)) {
            throw new InvalidArgumentType('$content', 'Content of ContentType');
        }

        $contentMetas = $this->innerComputeMetas($content, $field, $fallback);

        if ($fallback && !$this->customFallBackService) {
            $rootContent = $this->eZRepository->getLocationService()->loadLocation(
                $this->configResolver->getParameter('content.tree_root.location_id')
            )->getContent();

            // We need to load the good field too
            $metasIdentifier = $this->configResolver->getParameter('fieldtype_metas_identifier', 'nova_ezseo');
            $rootMetas = $this->innerComputeMetas($rootContent, $metasIdentifier, $fallback);

            foreach ($contentMetas as $key => $metaContent) {
                if (\array_key_exists($key, $rootMetas)) {
                    $metaContent->setContent(
                        $metaContent->isEmpty() ? $rootMetas[$key]->getContent() : $metaContent->getContent()
                    );
                }
            }
        }
    }

    /**
     * Compute Meta by reference.
     */
    protected function innerComputeMetas(
        Content $content,
                $field,
                &$needFallback = false
    ): array {
        if ($field instanceof Field) {
            $metasFieldValue = $field->value;
            $fieldDefIdentifier = $field->fieldDefIdentifier;
        } else {
            $metasFieldValue = $content->getFieldValue($field);
            $fieldDefIdentifier = $field;
        }

        if ($metasFieldValue instanceof MetasFieldValue) {
            $metasConfig = $this->configResolver->getParameter('fieldtype_metas', 'nova_ezseo');
            // as the configuration is the last fallback we need to loop on it.
            foreach (array_keys($metasConfig) as $metaName) {
                if ($metasFieldValue->nameExists($metaName)) {
                    $meta = $metasFieldValue->metas[$metaName];
                } else {
                    $meta = new Meta($metaName);
                    $metasFieldValue->metas[$metaName] = $meta;
                }

                /** @var Meta $meta */
                if ($meta->isEmpty()) {
                    $meta->setContent($metasConfig[$meta->getName()]['default_pattern']);
                    $fieldDefinition = $content->getContentType()->getFieldDefinition($fieldDefIdentifier);
                    $configuration = $fieldDefinition->getFieldSettings()['configuration'];
                    // but if we need something is the configuration we take it
                    if (isset($configuration[$meta->getName()]) && !empty($configuration[$meta->getName()])) {
                        $meta->setContent($configuration[$meta->getName()]);
                    }
                }
                if (!$this->metaNameSchema->resolveMeta($meta, $content)) {
                    $needFallback = true;
                }
            }

            return $metasFieldValue->metas;
        }

        return [];
    }
}
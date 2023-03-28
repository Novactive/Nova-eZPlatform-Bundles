<?php

namespace Novactive\Bundle\eZSEOBundle\GraphQL\Resolver;

use eZ\Publish\API\Repository\Values\Content\Content;
use GraphQL\Type\Definition\ResolveInfo;
use Novactive\Bundle\eZSEOBundle\Core\Meta;
use Novactive\Bundle\eZSEOBundle\GraphQL\Helper\NameHelper;
use Novactive\Bundle\eZSEOBundle\Service\MetaCompositionService;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class NovaEZSeoFieldResolver implements ResolverInterface, AliasedInterface
{
    protected MetaCompositionService $metaCompositionService;

    protected NameHelper $nameHelper;

    public function __construct(MetaCompositionService $metaCompositionService, NameHelper $nameHelper)
    {
        $this->metaCompositionService = $metaCompositionService;
        $this->nameHelper = $nameHelper;
    }

    public function resolveMetasFieldValue(Content $content, string $fieldDefinitionIdentifier)
    {
        $field = $content->getField($fieldDefinitionIdentifier);
        $metaValues = $this->metaCompositionService->computeMetasUsingFallback($field, $content);

        $output = [];

        /**
         * @var $metaObject Meta
         */
        foreach($metaValues as $definedMetaName => $metaObject)
        {
            $output[$this->nameHelper->sanitizeMetaFieldName($definedMetaName)] = $metaObject->getContent();
        }

        return $output;
    }

    public static function getAliases()
    {
        return [
            'resolveMetasFieldValue' => 'NovaSeoMetasFieldValue'
        ];
    }
}
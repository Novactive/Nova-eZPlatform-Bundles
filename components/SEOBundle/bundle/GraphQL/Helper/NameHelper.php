<?php

namespace Novactive\Bundle\eZSEOBundle\GraphQL\Helper;

use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

class NameHelper
{
    private CamelCaseToSnakeCaseNameConverter $caseConverter;

    /**
     * @param CamelCaseToSnakeCaseNameConverter $caseConverter
     */
    public function __construct(CamelCaseToSnakeCaseNameConverter $caseConverter)
    {
        $this->caseConverter = $caseConverter;
    }

    public function sanitizeMetaFieldName(string $metaFieldName)
    {
        $sanitizedFieldName = preg_replace("/[-\.:]/", "_", $metaFieldName);

        return $this->caseConverter->denormalize($sanitizedFieldName);
    }
}
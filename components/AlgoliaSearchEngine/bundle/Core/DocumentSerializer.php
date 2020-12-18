<?php

/**
 * Nova eZ Algolia Search Engine.
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @licence   "SEE FULL LICENSE OPTIONS IN LICENSE.md"
 *            Nova eZ Algolia Search Engine is tri-licensed, meaning you must choose one of three licenses to use:
 *                - Commercial License: a paid license, meant for commercial use. The default option for most users.
 *                - Creative Commons Non-Commercial No-Derivatives: meant for trial and non-commercial use.
 *                - GPLv3 License: meant for open-source projects.
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Core;

use eZ\Publish\Core\Search\Common\FieldNameGenerator;
use eZ\Publish\Core\Search\Common\FieldValueMapper;
use eZ\Publish\SPI\Search\Document;
use eZ\Publish\SPI\Search\FieldType\GeoLocationField;

final class DocumentSerializer
{
    /**
     * @var FieldValueMapper
     */
    private $fieldValueMapper;

    /**
     * @var FieldNameGenerator
     */
    private $nameGenerator;

    public function __construct(FieldValueMapper $fieldValueMapper, FieldNameGenerator $fieldNameGenerator)
    {
        $this->fieldValueMapper = $fieldValueMapper;
        $this->nameGenerator = $fieldNameGenerator;
    }

    public function serialize(Document $document): array
    {
        $body = [];
        $geolocFields = [];
        foreach ($document->fields as $field) {
            $fieldName = $this->nameGenerator->getTypedName($field->name, $field->type);
            if ($this->fieldValueMapper->canMap($field)) {
                $fieldValue = $this->fieldValueMapper->map($field);
            } else {
                $fieldValue = $field->value;
            }

            $body[$fieldName] = $fieldValue;

            if (
                $field->type instanceof GeoLocationField &&
                null !== $field->value['latitude'] && null !== $field->value['longitude']
            ) {
                $geolocFields[] = [
                    'lat' => $field->value['latitude'],
                    'lng' => $field->value['longitude'],
                ];
            }
        }
        if (\count($geolocFields) > 0) {
            $body['_geoloc'] = $geolocFields;
        }

        return $body;
    }
}

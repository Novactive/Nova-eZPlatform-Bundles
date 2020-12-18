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

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Event;

use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use Novactive\Bundle\eZAlgoliaSearchEngine\Mapping\Document;

final class ContentTranslationDataFieldConvertEvent extends DocumentCreateEvent
{
    /**
     * @var Content
     */
    private $content;

    /**
     * @var Field
     */
    private $field;

    /**
     * @var FieldDefinition
     */
    private $fieldDefinition;

    public function __construct(Content $content, Field $field, FieldDefinition $fieldDefinition, Document $document)
    {
        parent::__construct($document);
        $this->content = $content;
        $this->field = $field;
        $this->fieldDefinition = $fieldDefinition;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function getField(): Field
    {
        return $this->field;
    }

    public function getFieldDefinition(): FieldDefinition
    {
        return $this->fieldDefinition;
    }
}

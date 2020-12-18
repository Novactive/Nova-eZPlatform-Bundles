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

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\ResultExtractor;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\SPI\Persistence\Content\Handler as ContentHandler;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\ResultExtractor\FacetResultExtractor\FacetResultExtractor;

final class ContentResultsExtractor extends AbstractResultsExtractor
{
    public const CONTENT_ID_FIELD = 'content_id_i';

    /** @var ContentHandler */
    private $contentHandler;

    public function __construct(
        ContentHandler $contentHandler,
        FacetResultExtractor $facetResultExtractor,
        bool $skipMissingContentItems = true
    ) {
        parent::__construct($facetResultExtractor, $skipMissingContentItems);

        $this->contentHandler = $contentHandler;
    }

    protected function loadValueObject(array $document): ValueObject
    {
        return $this->contentHandler->loadContentInfo(
            (int) $document[self::CONTENT_ID_FIELD]
        );
    }

    public function getExpectedSourceFields(): array
    {
        return [
            self::MATCHED_TRANSLATION_FIELD,
            self::CONTENT_ID_FIELD,
        ];
    }
}

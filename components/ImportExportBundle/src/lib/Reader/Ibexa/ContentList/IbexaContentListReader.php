<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Ibexa\ContentList;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\ObjectAccessorBuilder;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator\ItemIterator;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\AbstractReader;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\Ibexa\IteratorItemTransformer\ContentSearchHitTransformerIterator;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderIteratorInterface;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter\ContentSearchAdapter;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Translation\TranslatableMessage;

class IbexaContentListReader extends AbstractReader implements TranslationContainerInterface
{
    protected ObjectAccessorBuilder $objectAccessorBuilder;
    protected SearchService $searchService;

    public function __construct(ObjectAccessorBuilder $objectAccessorBuilder, SearchService $searchService)
    {
        $this->objectAccessorBuilder = $objectAccessorBuilder;
        $this->searchService = $searchService;
    }

    public function __invoke(): ReaderIteratorInterface
    {
        /** @var IbexaContentListReaderOptions $options */
        $options = $this->getOptions();

        $criterions = [];
        if ($options->parentLocationId) {
            $criterions[] = new Query\Criterion\ParentLocationId(
                $options->parentLocationId
            );
        }
        if ($options->contentTypes) {
            $ids = [];
            $identifiers = [];
            foreach ($options->contentTypes as $contentType) {
                if (is_string($contentType)) {
                    $identifiers[] = $contentType;
                } else {
                    $ids[] = $contentType;
                }
            }

            if (!empty($ids)) {
                $criterions[] = new Query\Criterion\ContentTypeId($ids);
            }

            if (!empty($identifiers)) {
                $criterions[] = new Query\Criterion\ContentTypeIdentifier($identifiers);
            }
        }
        $query = new Query();
        if ($criterions) {
            $query->filter = new Query\Criterion\LogicalAnd($criterions);
        }

        $countQuery = clone $query;
        $countQuery->limit = 0;
        $searchResults = $this->searchService->findContent($countQuery);

        return new ItemIterator(
            $searchResults->totalCount,
            new BatchIterator(
                new ContentSearchAdapter($this->searchService, $query)
            ),
            new ContentSearchHitTransformerIterator(
                $this->objectAccessorBuilder
            )
        );
    }

    public static function getName(): TranslatableMessage
    {
        return new TranslatableMessage('reader.ibexa.content_list.name', [], 'import_export');
    }

    public static function getTranslationMessages(): array
    {
        return [(new Message('reader.ibexa.content_list.name', 'import_export'))->setDesc('Content list')];
    }

    public static function getOptionsFormType(): ?string
    {
        return IbexaContentListReaderOptionsFormType::class;
    }

    public static function getOptionsType(): ?string
    {
        return IbexaContentListReaderOptions::class;
    }
}

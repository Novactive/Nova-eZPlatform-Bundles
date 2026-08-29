<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Content;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\ObjectAccessorBuilder;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\ItemTransformer;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\SourceResolver;
use AlmaviaCX\Bundle\IbexaImportExport\Writer\AbstractWriter;
use Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @extends AbstractWriter<IbexaContentWriterOptions>
 */
class IbexaContentWriter extends AbstractWriter implements TranslationContainerInterface
{
    public function __construct(
        protected Repository $repository,
        protected IbexaContentImporter $contentImporter,
        protected ObjectAccessorBuilder $objectAccessorBuilder,
        SourceResolver $sourceResolver,
        ItemTransformer $itemTransformer,
    ) {
        parent::__construct($sourceResolver, $itemTransformer);
    }

    protected function getMappedItemInstance(): IbexaContentData
    {
        return new IbexaContentData();
    }

    /**
     * {@inheritDoc}
     *
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Content\IbexaContentData $mappedItem
     */
    protected function writeItem($item, $mappedItem)
    {
        /** @var array{action: string, content: Content}|null $contentImportResult */
        $contentImportResult = $this->repository->sudo(function (Repository $repository) use ($mappedItem) {
            try {
                return ($this->contentImporter)($mappedItem);
            } catch (ContentFieldValidationException $exception) {
                $newException = \Ibexa\Core\Base\Exceptions\ContentFieldValidationException::createNewWithMultiline(
                    $exception->getFieldErrors(),
                    $mappedItem->getContentRemoteId()
                );
                $this->logger->error($newException->getMessage());

                return null;
            }
        });

        if (!$contentImportResult) {
            return null;
        }

        $content = $contentImportResult['content'];
        $this->logger->info(
            'Imported content "'.$content->contentInfo->name.'" ('.$content->contentInfo->remoteId.')'
        );

        if ($contentImportResult['action']) {
            $imported_content_ids = $this->results->getResult('imported_content_ids');
            $imported_content_ids[$content->id] = [
                'name' => $content->getName(),
                'contentId' => $content->id,
                'action' => $contentImportResult['action'],
            ];
            $this->results->setResult('imported_content_ids', $imported_content_ids);
        }

        return $this->objectAccessorBuilder->buildFromContent($content);
    }

    public static function getName(): TranslatableMessage
    {
        return new TranslatableMessage('writer.ibexa.content.name', [], 'import_export');
    }

    public static function getTranslationMessages(): array
    {
        return [( new Message('writer.ibexa.content.name', 'import_export') )->setDesc('Ibexa content writer')];
    }

    public static function getResultTemplate(): ?string
    {
        return '@ibexadesign/import_export/writer/results/writer_ibexa_content.html.twig';
    }
}

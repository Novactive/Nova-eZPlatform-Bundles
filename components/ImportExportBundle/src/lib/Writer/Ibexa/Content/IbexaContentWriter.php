<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Content;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\ObjectAccessorBuilder;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\ItemTransformer;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\SourceResolver;
use AlmaviaCX\Bundle\IbexaImportExport\Reference\ReferenceBag;
use AlmaviaCX\Bundle\IbexaImportExport\Writer\AbstractWriter;
use Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException;
use Ibexa\Contracts\Core\Repository\Repository;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Translation\TranslatableMessage;

class IbexaContentWriter extends AbstractWriter implements TranslationContainerInterface
{
    protected Repository $repository;
    protected IbexaContentImporter $contentImporter;
    protected ObjectAccessorBuilder $objectAccessorBuilder;

    public function __construct(
        Repository $repository,
        IbexaContentImporter $contentImporter,
        ObjectAccessorBuilder $objectAccessorBuilder,
        SourceResolver $sourceResolver,
        ItemTransformer $itemTransformer,
        ReferenceBag $references
    ) {
        $this->repository = $repository;
        $this->contentImporter = $contentImporter;
        $this->objectAccessorBuilder = $objectAccessorBuilder;
        parent::__construct($sourceResolver, $itemTransformer, $references);
    }

    protected function getMappedItemInstance()
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
        /** @var \AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Content\IbexaContentWriterOptions $options */
        $options = $this->getOptions();

        $content = $this->repository->sudo(function (Repository $repository) use ($options, $mappedItem) {
            try {
                return ($this->contentImporter)($mappedItem, $options->allowUpdate);
//            } catch ( Throwable $exception) {
//                dd($item, $mappedItem, $exception);
            } catch (ContentFieldValidationException $exception) {
                $newException = \Ibexa\Core\Base\Exceptions\ContentFieldValidationException::createNewWithMultiline(
                    $exception->getFieldErrors(),
                    $mappedItem->getContentRemoteId()
                );
                $this->logger->notice('----> '.get_class($newException));
                $this->logger->notice($newException->getMessage());
                $this->logger->notice(print_r($newException->getFieldErrors(), true));
                $this->logger->notice(print_r($newException->getTraceAsString(), true));

                throw $exception;
            }
        });

        $this->logger->info(
            'Imported content "'.$content->contentInfo->name.'" ('.$content->contentInfo->remoteId.')'
        );

        $imported_content_ids = $this->results->getResult('imported_content_ids');
        $imported_content_ids[] = $content->id;
        $this->results->setResult('imported_content_ids', $imported_content_ids);

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

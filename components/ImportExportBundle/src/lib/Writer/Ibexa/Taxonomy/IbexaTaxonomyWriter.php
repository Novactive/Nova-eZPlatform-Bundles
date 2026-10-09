<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Taxonomy;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Taxonomy\TaxonomyAccessorBuilder;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\ItemTransformer;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\SourceResolver;
use AlmaviaCX\Bundle\IbexaImportExport\Writer\AbstractWriter;
use Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @extends AbstractWriter<IbexaTaxonomyWriterOptions>
 */
class IbexaTaxonomyWriter extends AbstractWriter implements TranslationContainerInterface
{
    public function __construct(
        protected Repository $repository,
        protected IbexaTaxonomyImporter $taxonomyImporter,
        protected TaxonomyAccessorBuilder $taxonomyAccessorBuilder,
        SourceResolver $sourceResolver,
        ItemTransformer $itemTransformer,
    ) {
        parent::__construct($sourceResolver, $itemTransformer);
    }

    protected function getMappedItemInstance(): IbexaTaxonomyData
    {
        return new IbexaTaxonomyData();
    }

    /**
     * {@inheritDoc}
     *
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Taxonomy\IbexaTaxonomyData $mappedItem
     */
    protected function writeItem($item, $mappedItem)
    {
        /** @var array{action: string, taxonomyEntry: TaxonomyEntry}|null $taxonomyEntryImportResult */
        $taxonomyEntryImportResult = $this->repository->sudo(function (Repository $repository) use ($mappedItem) {
            try {
                return ($this->taxonomyImporter)($mappedItem);
            } catch (ContentFieldValidationException $exception) {
                $newException = \Ibexa\Core\Base\Exceptions\ContentFieldValidationException::createNewWithMultiline(
                    $exception->getFieldErrors(),
                    $mappedItem->getContentRemoteId()
                );
                $this->logger->error($newException->getMessage());

                return null;
            }
        });

        if (!$taxonomyEntryImportResult) {
            return null;
        }

        $taxonomyEntry = $taxonomyEntryImportResult['taxonomyEntry'];
        $this->logger->info(
            'Imported taxonomy "'.$taxonomyEntry->getName().'" ('.$taxonomyEntry->getIdentifier().')'
        );

        if ($taxonomyEntryImportResult['action']) {
            $imported_content_ids = $this->results->getResult('imported_content_ids');
            $imported_content_ids[$taxonomyEntry->getContentId()] = [
                'name' => $taxonomyEntry->getName(),
                'contentId' => $taxonomyEntry->getContentId(),
                'action' => $taxonomyEntryImportResult['action'],
            ];
            $this->results->setResult('imported_content_ids', $imported_content_ids);
        }

        return $this->taxonomyAccessorBuilder->buildFromTaxonomyEntry($taxonomyEntry);
    }

    public static function getName(): TranslatableMessage
    {
        return new TranslatableMessage('writer.ibexa.taxonomy.name', [], 'import_export');
    }

    public static function getTranslationMessages(): array
    {
        return [( new Message('writer.ibexa.taxonomy.name', 'import_export') )->setDesc('Ibexa taxonomy writer')];
    }

    public static function getResultTemplate(): ?string
    {
        return '@ibexadesign/import_export/writer/results/writer_ibexa_content.html.twig';
    }
}

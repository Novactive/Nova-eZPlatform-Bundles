<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Taxonomy;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Taxonomy\TaxonomyAccessorBuilder;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\ItemTransformer;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\SourceResolver;
use AlmaviaCX\Bundle\IbexaImportExport\Reference\ReferenceBag;
use AlmaviaCX\Bundle\IbexaImportExport\Writer\AbstractWriter;
use Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException;
use Ibexa\Contracts\Core\Repository\Repository;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Translation\TranslatableMessage;

class IbexaTaxonomyWriter extends AbstractWriter implements TranslationContainerInterface
{
    protected Repository $repository;
    protected IbexaTaxonomyImporter $taxonomyImporter;
    protected TaxonomyAccessorBuilder $taxonomyAccessorBuilder;

    public function __construct(
        Repository $repository,
        IbexaTaxonomyImporter $taxonomyImporter,
        TaxonomyAccessorBuilder $taxonomyAccessorBuilder,
        SourceResolver $sourceResolver,
        ItemTransformer $itemTransformer,
        ReferenceBag $references
    ) {
        $this->repository = $repository;
        $this->taxonomyImporter = $taxonomyImporter;
        $this->taxonomyAccessorBuilder = $taxonomyAccessorBuilder;
        parent::__construct($sourceResolver, $itemTransformer, $references);
    }

    protected function getMappedItemInstance()
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
        /** @var \AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Taxonomy\IbexaTaxonomyWriterOptions $options */
        $options = $this->getOptions();

        /** @var \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry $taxonomyEntry */
        $taxonomyEntry = $this->repository->sudo(function (Repository $repository) use ($options, $mappedItem) {
            try {
                return ($this->taxonomyImporter)($mappedItem, $options->allowUpdate);
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
            'Imported taxonomy "'.$taxonomyEntry->getName().'" ('.$taxonomyEntry->getIdentifier().')'
        );

        $imported_content_ids = $this->results->getResult('imported_content_ids');
        $imported_content_ids[] = $taxonomyEntry->getContentId();
        $this->results->setResult('imported_content_ids', $imported_content_ids);

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

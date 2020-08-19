<?php

/**
 * NovaeZExtraBundle CheckController.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZSEOBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZExtraBundle\Command;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\Core\Helper\TranslationHelper;
use Novactive\Bundle\eZExtraBundle\Core\Manager\eZ\ContentType as ContentTypeManager;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateContentTypesCommand extends Command
{
    /**
     * @var Repository
     */
    private $eZPublishRepository;

    /**
     * @var TranslationHelper
     */
    private $translationHelper;

    /**
     * @var ContentTypeManager
     */
    private $contentTypeManager;

    /**
     * @required
     */
    public function setDependencies(
        Repository $eZPublishRepository,
        TranslationHelper $translationHelper,
        ContentTypeManager $contentTypeManager
    ): void {
        $this->eZPublishRepository = $eZPublishRepository;
        $this->translationHelper = $translationHelper;
        $this->contentTypeManager = $contentTypeManager;
    }

    protected function configure(): void
    {
        $this
            ->setName('novaezextra:contenttypes:create')
            ->setDescription('Create/Update the Content Types from an Excel Content Type Model')
            ->addArgument('file', InputArgument::REQUIRED, 'XLSX File to import')
            ->addArgument('tr', InputArgument::OPTIONAL, 'Translation of contentType (eng-GB, fre-FR...)')
            ->addArgument(
                'content_type_group_identifier',
                InputArgument::OPTIONAL,
                'Content type group identifier (Content, Contenu, Custom group...)'
            );
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $translation = $input->getArgument('tr');
        $availableLanguage = $this->translationHelper->getAvailableLanguages();
        if (\is_null($translation) || !\in_array($translation, $availableLanguage, true)) {
            $translation = 'eng-GB';
        }

        $filepath = $input->getArgument('file');
        if (!file_exists($filepath)) {
            $output->writeln("<error>{$filepath} not found.</error>");

            return false;
        }

        $oPHPExcel = IOFactory::load($filepath);
        if (!$oPHPExcel) {
            $output->writeln('<error>Failed to load data</error>');

            return false;
        }

        $contentTypeManager = $this->contentTypeManager;

        foreach ($oPHPExcel->getWorksheetIterator() as $oWorksheet) {
            $excludedTemplatesSheets = ['ContentType Template', 'FieldTypes'];
            if (\in_array($oWorksheet->getTitle(), $excludedTemplatesSheets)) {
                continue;
            }
            $output->writeln($oWorksheet->getTitle());

            // Mapping

            $lang = $translation;
            $contentTypeName = $oWorksheet->getCell('B2')->getValue();
            $contentTypeIdentifier = $oWorksheet->getCell('B3')->getValue();
            $contentTypeDescription = $oWorksheet->getCell('B4')->getValue();
            $contentTypeObjectPattern = $oWorksheet->getCell('B5')->getValue();
            $contentTypeURLPattern = $oWorksheet->getCell('B6')->getValue();
            $contentTypeContainer = 'yes' === $oWorksheet->getCell('B7')->getValue() ? true : false;

            if (!$contentTypeDescription) {
                $contentTypeDescription = 'Content Type Description - To be defined';
            }
            $contentTypeData = [
                'nameSchema' => $contentTypeObjectPattern,
                'urlAliasSchema' => $contentTypeURLPattern,
                'isContainer' => $contentTypeContainer,
                'names' => $contentTypeName,
                'descriptions' => $contentTypeDescription,
            ];
            $contentTypeFieldDefinitionsData = [];
            foreach ($oWorksheet->getRowIterator() as $row) {
                $rowIndex = $row->getRowIndex();
                $fieldIdentifier = $oWorksheet->getCell("B{$rowIndex}")->getValue();
                if (($rowIndex) >= 11 && ('' != $fieldIdentifier)) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
                    $contentTypeFieldsData = [];
                    foreach ($cellIterator as $cell) {
                        /** @var Cell $cell */
                        if (!\is_null($cell)) {
                            $cellValue = trim((string) $cell->getValue());
                            switch ($cell->getColumn()) {
                                case 'A':
                                    $contentTypeFieldsData['names'] = [$lang => $cellValue];
                                    break;
                                case 'B':
                                    $contentTypeFieldsData['identifier'] = $cellValue;
                                    break;
                                case 'C':
                                    $contentTypeFieldsData['type'] = $cellValue;
                                    break;
                                case 'D':
                                    if (!$cellValue) {
                                        $cellValue = '';
                                    }
                                    $contentTypeFieldsData['descriptions'] = [$lang => $cellValue];
                                    break;
                                case 'E':
                                    $contentTypeFieldsData['isRequired'] = 'yes' === $cellValue ? true : false;
                                    break;
                                case 'F':
                                    $contentTypeFieldsData['isSearchable'] = 'yes' === $cellValue ? true : false;
                                    break;
                                case 'G':
                                    $contentTypeFieldsData['isTranslatable'] = 'yes' === $cellValue ? true : false;
                                    break;
                                case 'H':
                                    $contentTypeFieldsData['fieldGroup'] = $cellValue;
                                    break;
                                case 'I':
                                    $contentTypeFieldsData['position'] = (int) $cellValue;
                                    break;
                                case 'J':
                                    $contentTypeFieldsData['settings'] = $cellValue;
                                    break;
                            }
                        }
                    }
                    $contentTypeFieldDefinitionsData[] = $contentTypeFieldsData;
                }
            }
            $contentTypeGroupIdentifierParam = $input->getArgument('content_type_group_identifier');
            $contentTypeGroups = $contentTypeManager->getContentTypeService()->loadContentTypeGroups();
            $contentTypeGroupIdentifier = null;
            foreach ($contentTypeGroups as $contentTypeGroup) {
                if (
                    !\is_null($contentTypeGroupIdentifierParam) &&
                    $contentTypeGroup->identifier === $contentTypeGroupIdentifierParam
                ) {
                    $contentTypeGroupIdentifier = $contentTypeGroupIdentifierParam;
                    break;
                }
            }
            $contentTypeGroupIdentifier = (!\is_null(
                $contentTypeGroupIdentifier
            ) ? $contentTypeGroupIdentifier : $contentTypeGroups[0]->identifier);
            try {
                $contentTypeManager->createUpdateContentType(
                    $contentTypeIdentifier,
                    $contentTypeGroupIdentifier,
                    $contentTypeData,
                    $contentTypeFieldDefinitionsData,
                    [],
                    $lang
                );
            } catch (\Exception $e) {
                $output->writeln("<error>{$e->getMessage()}</error>");
                $errors = $e->getFieldErrors();
                foreach ($errors as $attrIdentifier => $errorArray) {
                    $output->write("\t<info>{$contentTypeName}: {$attrIdentifier}</info>");
                    foreach ($errorArray as $error) {
                        /** @var ValidationError $error */
                        $message = $error->getTranslatableMessage()->message;
                        $output->writeln("\t<comment>{$message}</comment>");
                    }
                }
            }
        }
        $output->writeln('Done');

        return Command::SUCCESS;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->eZPublishRepository->getPermissionResolver()->setCurrentUserReference(
            $this->eZPublishRepository->getUserService()->loadUser(14)
        );
    }
}

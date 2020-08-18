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

namespace Novactive\Bundle\eZExtraBundle\Command;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\Base\Exceptions\ContentTypeFieldDefinitionValidationException;
use eZ\Publish\Core\FieldType\ValidationError;
use PHPExcel_Cell;
use PHPExcel_IOFactory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateContentTypesCommand.
 */
class CreateContentTypesCommand extends ContainerAwareCommand
{
    /**
     * Repository eZ Publish.
     *
     * @var Repository
     */
    protected $eZPublishRepository;

    /**
     * {@inheritdoc}
     */
    protected function configure()
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
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $translation = $input->getArgument('tr');
        $translationHelper = $this->getContainer()->get('ezpublish.translation_helper');
        $availableLanguage = $translationHelper->getAvailableLanguages();
        if (is_null($translation) || !in_array($translation, $availableLanguage)) {
            $translation = 'eng-GB';
        }

        $filepath = $input->getArgument('file');
        if (!file_exists($filepath)) {
            $output->writeln("<error>{$filepath} not found.</error>");

            return false;
        }

        $oPHPExcel = PHPExcel_IOFactory::load($filepath);
        if (!$oPHPExcel) {
            $output->writeln('<error>Failed to load data</error>');

            return false;
        }

        $contentTypeManager = $this->getContainer()->get('novactive.ezextra.content_type.manager');

        foreach ($oPHPExcel->getWorksheetIterator() as $oWorksheet) {
            $excludedTemplatesSheets = ['ContentType Template', 'FieldTypes'];
            if (in_array($oWorksheet->getTitle(), $excludedTemplatesSheets)) {
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
            $contentTypeContainer = 'yes' == $oWorksheet->getCell('B7')->getValue() ? true : false;

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
                        /** @var PHPExcel_Cell $cell */
                        if (!is_null($cell)) {
                            $cellValue = trim($cell->getValue());
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
                                    $contentTypeFieldsData['isRequired'] = 'yes' == $cellValue ? true : false;
                                    break;
                                case 'F':
                                    $contentTypeFieldsData['isSearchable'] = 'yes' == $cellValue ? true : false;
                                    break;
                                case 'G':
                                    $contentTypeFieldsData['isTranslatable'] = 'yes' == $cellValue ? true : false;
                                    break;
                                case 'H':
                                    $contentTypeFieldsData['fieldGroup'] = $cellValue;
                                    break;
                                case 'I':
                                    $contentTypeFieldsData['position'] = intval($cellValue);
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
                    !is_null($contentTypeGroupIdentifierParam) &&
                    $contentTypeGroup->attribute('identifier') == $contentTypeGroupIdentifierParam
                ) {
                    $contentTypeGroupIdentifier = $contentTypeGroupIdentifierParam;
                    break;
                }
            }
            $contentTypeGroupIdentifier = (!is_null(
                $contentTypeGroupIdentifier
            ) ? $contentTypeGroupIdentifier : $contentTypeGroups[0]->attribute('identifier'));
            try {
                $contentTypeManager->createUpdateContentType(
                    $contentTypeIdentifier,
                    $contentTypeGroupIdentifier,
                    $contentTypeData,
                    $contentTypeFieldDefinitionsData,
                    [],
                    $lang
                );
            } catch (ContentTypeFieldDefinitionValidationException $e) {
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
        unset($input); // phpmd tricks

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $input; // phpmd trick
        $output; // phpmd trick
        $this->eZPublishRepository = $this->getContainer()->get('ezpublish.api.repository');
        $this->eZPublishRepository->setCurrentUser($this->eZPublishRepository->getUserService()->loadUser(14));
    }
}

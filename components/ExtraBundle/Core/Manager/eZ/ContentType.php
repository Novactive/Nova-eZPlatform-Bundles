<?php

/**
 * NovaeZExtraBundle ContentType Manager.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

namespace Novactive\Bundle\eZExtraBundle\Core\Manager\eZ;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\ContentType\ContentType as ValueContentType;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeCreateStruct;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeUpdateStruct;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionUpdateStruct;
use eZ\Publish\Core\Base\Exceptions\BadStateException;

/**
 * Class ContentType.
 */
class ContentType
{
    /**
     * Repository eZ.
     *
     * @var Repository
     */
    protected $eZPublishRepository;

    /**
     * Constructor.
     */
    public function __construct(Repository $api)
    {
        $this->eZPublishRepository = $api;
    }

    /**
     * Get eZ Repository.
     *
     * @return Repository
     */
    public function getRepository()
    {
        return $this->eZPublishRepository;
    }

    /**
     * Easy access to the ContentService.
     *
     * @return ContentService
     */
    public function getContentService()
    {
        return $this->eZPublishRepository->getContentService();
    }

    /**
     * Easy access to the ContentTypeService.
     *
     * @return ContentTypeService
     */
    public function getContentTypeService()
    {
        return $this->eZPublishRepository->getContentTypeService();
    }

    /**
     * Change the user of the repository
     * Note: you need to keep the current user if you want to go back on the current user.
     */
    public function sudoRoot()
    {
        $this->eZPublishRepository->setCurrentUser($this->eZPublishRepository->getUserService()->loadUser(14));
    }

    /**
     * Create ContentType Wrapper.
     *
     * @param string $contentTypeIdentifier
     * @param string $contentTypeGroupIdentifier
     * @param array  $contentTypeData
     * @param array  $contentTypeFieldDefinitionsData
     * @param array  $options
     * @param string $lang
     */
    public function createContentType(
        $contentTypeIdentifier,
        $contentTypeGroupIdentifier,
        $contentTypeData,
        $contentTypeFieldDefinitionsData,
        $options = [],
        $lang = 'eng-US'
    ) {
        $contentTypeService = $this->getContentTypeService();
        $contentTypeGroup = $contentTypeService->loadContentTypeGroupByIdentifier($contentTypeGroupIdentifier);
        $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct($contentTypeIdentifier);

        if (!empty($options['remoteId'])) {
            $contentTypeCreateStruct->remoteId = $options['remoteId'];
        }

        $this->fillContentTypeStruct($contentTypeCreateStruct, $contentTypeData, $lang);
        $this->createFieldDefinitions($contentTypeCreateStruct, $contentTypeFieldDefinitionsData, $lang);
        $contentTypeDraft = $contentTypeService->createContentType($contentTypeCreateStruct, [$contentTypeGroup]);
        $this->publishContentType($contentTypeDraft);
    }

    /**
     * Update ContentType Wrapper.
     *
     * @param array  $contentTypeData
     * @param array  $contentTypeFieldDefinitionsData
     * @param array  $options
     * @param string $lang
     */
    public function updateContentType(
        ValueContentType $contentType,
        $contentTypeData,
        $contentTypeFieldDefinitionsData,
        $options = [],
        $lang = 'eng-US'
    ) {
        $contentTypeService = $this->getContentTypeService();

        try {
            $contentTypeDraft = $contentTypeService->createContentTypeDraft($contentType);
        } catch (BadStateException $e) {
            $contentTypeDraft = $contentTypeService->loadContentTypeDraft($contentType->id);
        }
        $contentTypeUpdateStruct = $contentTypeService->newContentTypeUpdateStruct();
        $this->fillContentTypeStruct($contentTypeUpdateStruct, $contentTypeData, $lang);
        $this->updateFieldDefinitions(
            $contentTypeDraft,
            $contentTypeFieldDefinitionsData,
            $lang
        );
        $contentTypeService->updateContentTypeDraft($contentTypeDraft, $contentTypeUpdateStruct);
        $this->publishContentType($contentTypeDraft);
    }

    /**
     * Fill the Struct according to the Public API.
     *
     * @param ContentTypeCreateStruct|ContentTypeUpdateStruct $struct
     * @param array                                           $contentTypeData
     * @param string                                          $lang
     */
    protected function fillContentTypeStruct($struct, $contentTypeData, $lang)
    {
        $struct->mainLanguageCode = $lang;
        $struct->nameSchema = $contentTypeData['nameSchema'];
        $struct->isContainer = $contentTypeData['isContainer'];
        $struct->urlAliasSchema = $contentTypeData['urlAliasSchema'];

        if (!is_array($contentTypeData['names'])) {
            $contentTypeData['names'] = [$lang => $contentTypeData['names']];
        }
        $struct->names = $contentTypeData['names'];

        if (!is_array($contentTypeData['descriptions'])) {
            $contentTypeData['descriptions'] = [$lang => $contentTypeData['descriptions']];
        }
        $struct->descriptions = $contentTypeData['descriptions'];
    }

    /**
     * Create and assign the fieldDefinition to the Structure.
     *
     * @param array  $contentTypeFieldDefinitionsData
     * @param string $lang
     */
    protected function createFieldDefinitions(
        ContentTypeCreateStruct $contentTypeCreateStruct,
        $contentTypeFieldDefinitionsData,
        $lang
    ) {
        $contentTypeService = $this->getContentTypeService();
        foreach ($contentTypeFieldDefinitionsData as $definition) {
            $fieldCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct(
                $definition['identifier'],
                $definition['type']
            );
            $this->fillFieldDefinitionStruct($fieldCreateStruct, $definition, $lang);
            $contentTypeCreateStruct->addFieldDefinition($fieldCreateStruct);
        }
    }

    /**
     * Add/Update or remove FieldDefinitions from/to/in the Structure.
     *
     * @param array  $contentTypeFieldDefinitionsData
     * @param string $lang
     */
    protected function updateFieldDefinitions(
        ContentTypeDraft $contentTypeDraft,
        $contentTypeFieldDefinitionsData,
        $lang
    ) {
        $contentTypeService = $this->getContentTypeService();

        $remainingFieldDefinitions = [];
        foreach ($contentTypeDraft->fieldDefinitions as $fieldDefinition) {
            $remainingFieldDefinitions[$fieldDefinition->identifier] = 'existing';
        }
        foreach ($contentTypeFieldDefinitionsData as $definition) {
            $fieldDefinition = $contentTypeDraft->getFieldDefinition($definition['identifier']);
            if ($fieldDefinition instanceof FieldDefinition) {
                $fieldUpdateStruct = $contentTypeService->newFieldDefinitionUpdateStruct();
                $this->fillFieldDefinitionStruct($fieldUpdateStruct, $definition, $lang);
                $contentTypeService->updateFieldDefinition($contentTypeDraft, $fieldDefinition, $fieldUpdateStruct);
            } else {
                $fieldCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct(
                    $definition['identifier'],
                    $definition['type']
                );
                $this->fillFieldDefinitionStruct($fieldCreateStruct, $definition, $lang);
                $contentTypeService->addFieldDefinition($contentTypeDraft, $fieldCreateStruct);
            }
            unset($remainingFieldDefinitions[$definition['identifier']]);
        }
        // delete the remaining
        foreach ($remainingFieldDefinitions as $fieldDefinitionIdentifier => $v) {
            $contentTypeService->removeFieldDefinition(
                $contentTypeDraft,
                $contentTypeDraft->getFieldDefinition($fieldDefinitionIdentifier)
            );
        }
    }

    /**
     * Fill the Struct according to the Public API.
     *
     * @param FieldDefinitionCreateStruct|FieldDefinitionUpdateStruct $struct
     * @param array                                                   $definition
     * @param string                                                  $lang
     */
    protected function fillFieldDefinitionStruct($struct, $definition, $lang)
    {
        $struct->fieldGroup = $definition['fieldGroup'];
        $struct->position = $definition['position'];
        $struct->isTranslatable = $definition['isTranslatable'];
        $struct->isRequired = $definition['isRequired'];
        $struct->isSearchable = $definition['isSearchable'];

        if (!is_array($definition['names'])) {
            $definition['names'] = [$lang => $definition['names']];
        }
        $struct->names = $definition['names'];

        if (!is_array($definition['descriptions'])) {
            $definition['descriptions'] = [$lang => $definition['descriptions']];
        }
        $struct->descriptions = $definition['descriptions'];

        if ($definition['settings']) {
            $settings = [];
            $lines = explode("\n", $definition['settings']);
            foreach ($lines as $line) {
                preg_match('/(\\s*)([a-zA-Z]*)(\\s*):(\\s*)\\[?([^\\[\\]]*)\\]?(\\s*)/uisx', $line, $matches);
                $key = trim($matches[2]);
                $value = explode(',', trim($matches[5]));
                array_walk(
                    $value,
                    function (&$value, &$key) {
                        $value = trim($value);
                    }
                );
                if ('' != $key) {
                    $settings[$key] = $value;
                }
            }
            $this->fillExtraSettings($struct, $settings, $definition['type'], $lang);
        }
    }

    /**
     * Fill the Extra Struct according to the Public API.
     *
     * @param FieldDefinitionCreateStruct|FieldDefinitionUpdateStruct $struct
     * @param string                                                  $lang
     */
    protected function fillExtraSettings($struct, $settings, $fieldTypeIdentifier, $lang)
    {
        if ('ezselection' == $fieldTypeIdentifier) {
            $isMultiple = false;
            if (isset($settings['Multiple']) && ($isM = $settings['Multiple'])) {
                $isMultiple = 'Y' == $isM[0] ? true : false;
            }
            if (isset($settings['List']) && ($list = $settings['List'])) {
                $struct->fieldSettings = [
                    'isMultiple' => $isMultiple,
                    'options' => $list,
                ];
            }
        }

        if ('ezobjectrelationlist' == $fieldTypeIdentifier) {
            if ($to = $settings['To']) {
                $struct->fieldSettings['selectionContentTypes'] = $to;
            }
            if (isset($settings['DefaultLocation']) && ($defaultLocation = $settings['DefaultLocation'])) {
                // just the first is used
                if ($alias = $defaultLocation[0]) {
                    try {
                        $urlAlias = $this->getRepository()->getURLAliasService()->lookup($alias);
                        $struct->fieldSettings['selectionDefaultLocation'] = $urlAlias->destination;
                    } catch (NotFoundException $e) {
                        unset($struct->fieldSettings['selectionDefaultLocation']);
                    }
                }
            }
        }
        if ('ezobjectrelation' == $fieldTypeIdentifier) {
            if (
                isset($settings['BrowseMode']) &&
                (false !== strpos(strtolower(implode('', $settings['BrowseMode'])), 'dropdownlist'))
            ) {
                $struct->fieldSettings['selectionMethod'] = 1;
            } else {
                $struct->fieldSettings['selectionMethod'] = 0;
            }

            if (isset($settings['DefaultLocation']) && ($defaultLocation = $settings['DefaultLocation'])) {
                // just the first is used
                if ($alias = $defaultLocation[0]) {
                    try {
                        $urlAlias = $this->getRepository()->getURLAliasService()->lookup(
                            $alias
                        );
                        $struct->fieldSettings['selectionRoot'] = $urlAlias->destination;
                    } catch (NotFoundException $e) {
                        unset($struct->fieldSettings['selectionRoot']);
                    }
                }
            }
        }
    }

    /**
     * Publish/Save a ContentTypeDraft.
     */
    public function publishContentType(ContentTypeDraft $contentTypeDraft)
    {
        $this->getContentTypeService()->publishContentTypeDraft($contentTypeDraft);
    }

    /**
     * Create/Update Sugar for trying to update else to create.
     *
     * @param string $contentTypeIdentifier
     * @param string $contentTypeGroupIdentifier
     * @param array  $contentTypeData
     * @param array  $contentTypeFieldDefinitionsData
     * @param array  $options
     * @param string $lang
     */
    public function createUpdateContentType(
        $contentTypeIdentifier,
        $contentTypeGroupIdentifier,
        $contentTypeData,
        $contentTypeFieldDefinitionsData,
        $options = [],
        $lang = 'eng-US'
    ) {
        $contentTypeService = $this->getContentTypeService();

        try {
            $contentType = $contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
            if ((array_key_exists('do_no_update', $options)) && (true == $options['do_no_update'])) {
                return;
            }
            $this->updateContentType(
                $contentType,
                $contentTypeData,
                $contentTypeFieldDefinitionsData,
                $options,
                $lang
            );
            if ((array_key_exists('callback_update', $options)) && (is_callable($options['callback_update']))) {
                $options['callback_update']($contentType);
            }
        } catch (NotFoundException $e) {
            $this->createContentType(
                $contentTypeIdentifier,
                $contentTypeGroupIdentifier,
                $contentTypeData,
                $contentTypeFieldDefinitionsData,
                $options,
                $lang
            );
            if ((array_key_exists('callback_create', $options)) && (is_callable($options['callback_create']))) {
                $options['callback_create'](
                    $contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier)
                );
            }
        }
    }

    /**
     * Get + Create (no update) Sugar for trying to update else to create.
     *
     * @param string $contentTypeIdentifier
     * @param string $contentTypeGroupIdentifier
     * @param array  $contentTypeData
     * @param array  $contentTypeFieldDefinitionsData
     * @param array  $options
     * @param string $lang
     *
     * @return ValueContentType
     */
    public function getCreateContentType(
        $contentTypeIdentifier,
        $contentTypeGroupIdentifier,
        $contentTypeData,
        $contentTypeFieldDefinitionsData,
        $options = [],
        $lang = 'eng-US'
    ) {
        $options['do_no_update'] = true;
        $contentTypeService = $this->getContentTypeService();
        $this->createUpdateContentType(
            $contentTypeIdentifier,
            $contentTypeGroupIdentifier,
            $contentTypeData,
            $contentTypeFieldDefinitionsData,
            $options,
            $lang
        );

        return $contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
    }
}

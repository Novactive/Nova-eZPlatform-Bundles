<?php

/**
 * NovaeZEnhancedImageAssetBundle.
 *
 * @package   NovaeZEnhancedImageAssetBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZEnhancedImageAssetBundle/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\FieldTypeService;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\FieldType\Mapper\ImageFormMapper;
use Novactive\EzEnhancedImageAsset\Form\Type\FieldType\EnhancedImageFieldType;
use Symfony\Component\Form\FormInterface;

class FormMapper extends ImageFormMapper
{
    /** @var FieldTypeService */
    private $fieldTypeService;

    /**
     * @required
     */
    public function setFieldTypeService(FieldTypeService $fieldTypeService): void
    {
        $this->fieldTypeService = $fieldTypeService;
    }

    /**
     * @throws NotFoundException
     */
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig      = $fieldForm->getConfig();
        $fieldType       = $this->fieldTypeService->getFieldType($fieldDefinition->fieldTypeIdentifier);
        $names           = $fieldDefinition->getNames();
        $label           = $fieldDefinition->getName($formConfig->getOption('mainLanguageCode')) ?: reset($names);

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                           ->create(
                               'value',
                               EnhancedImageFieldType::class,
                               [
                                   'required' => $fieldDefinition->isRequired,
                                   'label'    => $label,
                               ]
                           )
                           ->addModelTransformer(new ValueTransformer($fieldType, $data->value, Value::class))
                           ->setAutoInitialize(false)
                           ->getForm()
            );
    }
}

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

use Ibexa\AdminUi\FieldType\Mapper\ImageFormMapper;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\ContentForms\FieldType\FieldValueFormMapperInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\FieldTypeService;
use Novactive\EzEnhancedImageAsset\Form\Type\FieldType\EnhancedImageFieldType;
use Symfony\Component\Form\FormInterface;

class FormMapper extends ImageFormMapper implements FieldValueFormMapperInterface
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
        $formConfig = $fieldForm->getConfig();
        $fieldType = $this->fieldTypeService->getFieldType($fieldDefinition->fieldTypeIdentifier);
        $names = $fieldDefinition->getNames();
        $label = $fieldDefinition->getName($formConfig->getOption('mainLanguageCode')) ?: reset($names);

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                           ->create(
                               'value',
                               EnhancedImageFieldType::class,
                               [
                                   'required' => $fieldDefinition->isRequired,
                                   'label' => $label,
                               ]
                           )
                           ->addModelTransformer(new ValueTransformer($fieldType, $data->value, Value::class))
                           ->setAutoInitialize(false)
                           ->getForm()
            );
    }
}

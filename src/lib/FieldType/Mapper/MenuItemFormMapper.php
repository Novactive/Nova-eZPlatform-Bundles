<?php

namespace Novactive\EzMenuManager\FieldType\Mapper;

use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use Novactive\EzMenuManager\Form\Type\FieldType\MenuItemFieldType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuItemFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldDefinitionForm
            ->add(
                $fieldDefinitionForm
                    ->getConfig()->getFormFactory()->createBuilder()
                    ->create(
                        'defaultValue',
                        MenuItemFieldType::class,
                        [
                            'required' => false,
                            'label'    => 'field_definition.ezstring.default_value',
                        ]
                    )
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig      = $fieldForm->getConfig();
        $names           = $fieldDefinition->getNames();
        $label           = $fieldDefinition->getName($formConfig->getOption('mainLanguageCode')) ?: reset($names);
        //        $validatorConfiguration = $fieldDefinition->getValidatorConfiguration();

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                           ->create(
                               'value',
                               MenuItemFieldType::class,
                               [
                                   'required' => $fieldDefinition->isRequired,
                                   'label'    => $label,
                               ]
                           )
                           ->setAutoInitialize(false)
                           ->getForm()
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'translation_domain' => 'ezrepoforms_content_type',
                ]
            );
    }
}

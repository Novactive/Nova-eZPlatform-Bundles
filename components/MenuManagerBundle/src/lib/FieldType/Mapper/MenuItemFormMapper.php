<?php
/**
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

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

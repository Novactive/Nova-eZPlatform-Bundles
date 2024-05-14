<?php

namespace AlmaviaCX\Ibexa\Bundle\FieldTypes\FieldType\AcxSelection;

use AlmaviaCX\Ibexa\Bundle\FieldTypes\FieldType\AcxSelection\Form\AcxSelectionSettingsType;
use AlmaviaCX\Ibexa\Bundle\FieldTypes\FieldType\AcxSelection\Form\AcxSelectionType;
use AlmaviaCX\Ibexa\Bundle\FieldTypes\Service\SelectionInterface;
use Ibexa\AdminUi\FieldType\FieldDefinitionFormMapperInterface;
use Ibexa\AdminUi\Form\Data\FieldDefinitionData;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\ContentForms\FieldType\FieldValueFormMapperInterface;
use Ibexa\Contracts\Core\FieldType\Generic\Type as GenericType;
use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\FieldType\Indexable;
use Ibexa\Contracts\Core\FieldType\ValueSerializerInterface;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition as SPIFieldDefinition;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Symfony\Component\Form\FormInterface;
use Ibexa\Contracts\Core\Search;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Type extends GenericType implements FieldValueFormMapperInterface, FieldDefinitionFormMapperInterface,  Indexable
{
    public const IDENTIFIER = 'acxselection';


    public function __construct(ValueSerializerInterface $serializer, ValidatorInterface $validator, private readonly SelectionInterface $selectionService)
    {
        parent::__construct($serializer, $validator);
    }

    public function getFieldTypeIdentifier(): string
    {
        return self::IDENTIFIER;
    }
    public function getSettingsSchema(): array
    {
        return [
            'choices_entry' => [
                'type' => 'string',
                'default' => 'default',
            ],
            'template' => [
                'type' => 'string',
                'default' => 'default',
            ],
            'isMultiple' => [
                'type' => 'bool',
                'default' => false,
            ],
        ];
    }

    public function fromHash($hash): Value
    {
        return new Value((array)$hash);
    }

    public function toHash(SPIValue $value): ?array
    {
        if (!($value instanceof Value)) {
            return [];
        }
        return (array) $value->selection;
    }
    
    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        if (empty($value->selection)) {
            return '';
        }

        $names = [];
        $fieldSettings = $fieldDefinition->getFieldSettings();

        $choices = array_flip($this->selectionService->getChoices((string)$fieldSettings['choices_entry']));
        foreach ($value->selection as $selectedName) {
            $name = $choices[$selectedName]?? null;
            if ($name === null) {
                continue;
            }
            $names[]= $name;
        }
        return implode(' ', array_filter($names));
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data): void
    {
        $definition = $data->fieldDefinition;
        $fieldSettings = $definition->getFieldSettings();
        $fieldForm->add('value', AcxSelectionType::class, [
            'required' => $definition->isRequired,
            'label' => $definition->getName(),
            'multiple' => $fieldSettings['isMultiple']?? true,
            'choices' => $this->selectionService->getChoices((string)$fieldSettings['choices_entry'])
        ]);
    }
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $fieldDefinitionForm->add('fieldSettings', AcxSelectionSettingsType::class, [
            'label' => false,
        ]);
    }
    public function isSearchable(): bool
    {
        return true;
    }

    public function getIndexData(Field $field, SPIFieldDefinition $fieldDefinition): array
    {
        $fieldValue = $field->value->data??[];
        $fieldValue = (string)reset($fieldValue);
        $values = (array)$field->value->data;
        return [
            new Search\Field(
                'value',
                $fieldValue,
                new Search\FieldType\StringField()
            ),
            new Search\Field(
                'values',
                $values,
                new Search\FieldType\MultipleStringField()
            ),
            new Search\Field(
                'fulltext',
                $values,
                new Search\FieldType\FullTextField()
            ),
        ];
    }

    public function getIndexDefinition(): array
    {
        return [
            'value' => new Search\FieldType\StringField(),
            'values' => new Search\FieldType\MultipleStringField(),
        ];
    }

    public function getDefaultMatchField(): string
    {
        return 'value';
    }

    public function getDefaultSortField(): string
    {
        return $this->getDefaultMatchField();
    }
}
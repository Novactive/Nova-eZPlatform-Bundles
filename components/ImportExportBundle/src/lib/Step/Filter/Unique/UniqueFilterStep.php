<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Step\Filter\Unique;

use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\SourceResolver;
use AlmaviaCX\Bundle\IbexaImportExport\Step\AbstractStep;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Translation\TranslatableMessage;

class UniqueFilterStep extends AbstractStep implements TranslationContainerInterface
{
    protected array $values = [];
    protected SourceResolver $sourceResolver;

    public function __construct(
        SourceResolver $sourceResolver
    ) {
        $this->sourceResolver = $sourceResolver;
    }

    public function processItem($item)
    {
        $value = $this->sourceResolver->getPropertyValue(
            $item,
            new PropertyPath($this->getOption('propertyPath'))
        );
        if (in_array($value, $this->values, true)) {
            return false;
        }
        $this->values[] = $value;

        return $item;
    }

    public static function getName(): TranslatableMessage
    {
        return new TranslatableMessage('step.filter.unique.name', [], 'import_export');
    }

    public static function getTranslationMessages(): array
    {
        return [( new Message('step.filter.unique.name', 'import_export') )->setDesc('Unique filter')];
    }

    public static function getOptionsType(): ?string
    {
        return UniqueFilterStepOptions::class;
    }
}

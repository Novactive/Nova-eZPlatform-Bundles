<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Step\Filter\NotEmpty;

use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\SourceResolver;
use AlmaviaCX\Bundle\IbexaImportExport\Step\AbstractStep;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @extends AbstractStep<NotEmptyFilterStepOptions>
 */
class NotEmptyFilterStep extends AbstractStep implements TranslationContainerInterface
{
    public function __construct(
        protected SourceResolver $sourceResolver
    ) {
    }

    public function processItem($item): mixed
    {
        $value = $this->sourceResolver->getPropertyValue(
            $item,
            new PropertyPath($this->getOption('propertyPath'))
        );
        if (empty($value)) {
            return false;
        }

        return $item;
    }

    public static function getName(): TranslatableMessage
    {
        return new TranslatableMessage('step.filter.not_empty.name', [], 'import_export');
    }

    public static function getTranslationMessages(): array
    {
        return [( new Message('step.filter.not_empty.name', 'import_export') )->setDesc('Not empty filter')];
    }

    public static function getOptionsType(): string
    {
        return NotEmptyFilterStepOptions::class;
    }
}

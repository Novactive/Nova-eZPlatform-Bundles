<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Step\Callback;

use AlmaviaCX\Bundle\IbexaImportExport\Step\AbstractStep;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @extends AbstractStep<CallbackStepOptions>
 */
class CallbackStep extends AbstractStep implements TranslationContainerInterface
{
    public function processItem($item): mixed
    {
        /** @var \AlmaviaCX\Bundle\IbexaImportExport\Step\Callback\CallbackStepOptions $options */
        $options = $this->getOptions();

        return call_user_func($options->callback, $item, $this->getReferenceBag());
    }

    public static function getName(): TranslatableMessage
    {
        return new TranslatableMessage('step.callback.name', [], 'import_export');
    }

    public static function getTranslationMessages(): array
    {
        return [( new Message('step.callback.name', 'import_export') )->setDesc('Callback')];
    }
}

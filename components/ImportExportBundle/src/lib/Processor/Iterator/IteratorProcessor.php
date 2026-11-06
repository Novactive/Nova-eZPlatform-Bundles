<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Processor\Iterator;

use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\SourceResolver;
use AlmaviaCX\Bundle\IbexaImportExport\Monolog\WorkflowLoggerInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\AbstractProcessor;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowState;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * This processor is used to iterate over an array and apply a processor to each value.
 *
 * @extends AbstractProcessor<IteratorProcessorOptions>
 */
class IteratorProcessor extends AbstractProcessor
{
    public function __construct(
        protected SourceResolver $sourceResolver
    ) {
    }

    /**
     * @throws \AlmaviaCX\Bundle\IbexaImportExport\Exception\SourceResolutionException
     */
    public function processItem($item): mixed
    {
        $options = $this->getOptions();

        $values = ($this->sourceResolver)($options->value, $item, $this->getReferenceBag());
        if (!is_array($values)) {
            $values = [$values];
        }

        foreach ($values as $value) {
            $iterationItem = [
                'iteration_value' => $value,
                'item' => $item,
            ];

            ($options->processor)($iterationItem);
        }

        return $item;
    }

    public function setIdentifier(string $identifier): void
    {
        parent::setIdentifier($identifier);

        $processor = $this->getOption('processor', null);
        $processor?->setIdentifier(sprintf('%s.processor', $identifier));
    }

    public static function getName(): TranslatableMessage
    {
        return new TranslatableMessage(/* @Desc("Iterator") */ 'processor.iterator.name');
    }

    public static function getOptionsType(): string
    {
        return IteratorProcessorOptions::class;
    }

    public function setLogger(WorkflowLoggerInterface $logger): void
    {
        parent::setLogger($logger);
        $processor = $this->getOption('processor', null);
        $processor?->setLogger($logger);
    }

    public function setState(WorkflowState $state): void
    {
        parent::setState($state);
        $processor = $this->getOption('processor', null);
        $processor?->setState($state);
    }
}

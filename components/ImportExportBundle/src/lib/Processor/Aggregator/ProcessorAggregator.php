<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Processor\Aggregator;

use AlmaviaCX\Bundle\IbexaImportExport\Monolog\WorkflowLoggerInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\AbstractProcessor;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Translation\TranslatableMessage;

class ProcessorAggregator extends AbstractProcessor implements ProcessorInterface
{
    /**
     * {@inheritDoc}
     */
    public function processItem($item)
    {
        $processors = $this->getProcessors();
        foreach ($processors as $processor) {
            $item = ($processor)($item);
            if (false === $item) {
                return;
            }
        }
    }

    /**
     * @return array<ProcessorInterface>
     */
    public function getProcessors(): array
    {
        return $this->getOption('processors', []);
    }

    public function setLogger(WorkflowLoggerInterface $logger): void
    {
        parent::setLogger($logger);
        $processors = $this->getProcessors();
        foreach ($processors as $processor) {
            $processor->setLogger($logger);
        }
    }

    public function getIdentifier(): string
    {
        return 'processor.aggregator';
    }

    public static function getName(): TranslatableMessage
    {
        return new TranslatableMessage(/* @Desc("Aggregator") */ 'processor.aggregator.name');
    }

    public static function getOptionsType(): ?string
    {
        return ProcessorAggregatorOptions::class;
    }
}

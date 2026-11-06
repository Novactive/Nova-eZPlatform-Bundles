<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Processor\Aggregator;

use AlmaviaCX\Bundle\IbexaImportExport\Monolog\WorkflowLoggerInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\AbstractProcessor;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowState;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Translation\TranslatableMessage;
use Throwable;

/**
 * Processor used to fork the processing of an item to multiple processors.
 *
 * @extends AbstractProcessor<ProcessorAggregatorOptions>
 * @implements ProcessorInterface<ProcessorAggregatorOptions>
 */
class ProcessorAggregator extends AbstractProcessor implements ProcessorInterface
{
    /**
     * @throws \Throwable
     */
    public function processItem($item): mixed
    {
        $processors = $this->getProcessors();
        try {
            foreach ($processors as $processor) {
                $item = ($processor)($item);
                if (false === $item) {
                    return null;
                }
            }
        } catch (Throwable $e) {
            if ($this->getOption('errorBubbling', true)) {
                throw $e;
            }
            $this->logger->logException($e);
        }

        return null;
    }

    public function setIdentifier(string $identifier): void
    {
        parent::setIdentifier($identifier);

        $processors = $this->getProcessors();
        foreach ($processors as $processorIdentifier => $processor) {
            $processor->setIdentifier($processorIdentifier);
        }
    }

    /**
     * @return array<ProcessorInterface<ProcessorOptions>>
     */
    public function getProcessors(): array
    {
        $options = $this->getOptions();

        return $options->processors;
    }

    public function setLogger(WorkflowLoggerInterface $logger): void
    {
        parent::setLogger($logger);
        $processors = $this->getProcessors();
        foreach ($processors as $processor) {
            $processor->setLogger($logger);
        }
    }

    public function setState(WorkflowState $state): void
    {
        parent::setState($state);
        $processors = $this->getProcessors();
        foreach ($processors as $processor) {
            $processor->setState($state);
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

    public static function getOptionsType(): string
    {
        return ProcessorAggregatorOptions::class;
    }
}

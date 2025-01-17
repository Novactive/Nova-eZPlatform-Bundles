<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Processor\Iterator;

use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\SourceResolver;
use AlmaviaCX\Bundle\IbexaImportExport\Monolog\WorkflowLoggerInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\AbstractProcessor;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface;
use Symfony\Component\Translation\TranslatableMessage;

class IteratorProcessor extends AbstractProcessor implements ProcessorInterface
{
    protected SourceResolver $sourceResolver;

    public function __construct(
        SourceResolver $sourceResolver
    ) {
        $this->sourceResolver = $sourceResolver;
    }

    public function processItem($item)
    {
        /** @var ProcessorInterface $processor */
        $processor = $this->getOption('processor', []);
        $source = $this->getOption('value', []);

        $values = ($this->sourceResolver)($source, $item);
        if (!is_array($values)) {
            $values = [$values];
        }

        foreach ($values as $value) {
            ($processor)(
                [
                    'iteration_value' => $value,
                    'item' => $item,
                ]
            );
        }

        return $item;
    }

    public static function getName()
    {
        return new TranslatableMessage(/* @Desc("Iterator") */ 'processor.iterator.name');
    }

    public static function getOptionsType(): ?string
    {
        return IteratorProcessorOptions::class;
    }

    public function setLogger(WorkflowLoggerInterface $logger): void
    {
        parent::setLogger($logger);
        $processor = $this->getOption('processor', []);
        $processor->setLogger($logger);
    }
}

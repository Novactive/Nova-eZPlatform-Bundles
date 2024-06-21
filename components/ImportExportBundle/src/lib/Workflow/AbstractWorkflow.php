<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow;

use AlmaviaCX\Bundle\IbexaImportExport\Monolog\WorkflowLoggerInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Reference\Reference;
use AlmaviaCX\Bundle\IbexaImportExport\Reference\ReferenceBag;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\Form\Type\WorkflowProcessConfigurationFormType;
use DateTimeImmutable;
use LimitIterator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Throwable;

abstract class AbstractWorkflow implements WorkflowInterface
{
    protected WorkflowLoggerInterface $logger;
    protected WorkflowExecutionConfiguration $configuration;
    protected EventDispatcherInterface $dispatcher;
    protected ReferenceBag $referenceBag;

    protected DateTimeImmutable $startTime;
    protected DateTimeImmutable $endTime;
    protected array $writerResults = [];
    protected int $totalItemsCount = 0;
    protected int $offset = 0;
    protected float $progress = 0;
    protected bool $debug = false;

    public function __construct(ReferenceBag $references, EventDispatcherInterface $dispatcher)
    {
        $this->referenceBag = $references;
        $this->dispatcher = $dispatcher;
    }

    public function addEventListener(string $eventName, callable $listener, int $priority = 0): AbstractWorkflow
    {
        $this->dispatcher->addListener($eventName, $listener, $priority);

        return $this;
    }

    /**
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowExecutionConfiguration $configuration
     */
    public function setConfiguration(WorkflowExecutionConfiguration $configuration): void
    {
        $this->configuration = $configuration;
    }

    protected function prepare(): void
    {
        $this->dispatcher->dispatch(new WorkflowEvent($this), WorkflowEvent::PREPARE);
    }

    protected function finish(): void
    {
        $this->dispatcher->dispatch(new WorkflowEvent($this), WorkflowEvent::FINISH);
    }

    public function __invoke(int $batchLimit = -1): void
    {
        $this->prepare();

        $this->startTime = new DateTimeImmutable();

        try {
            $this->referenceBag->resetScope(Reference::SCOPE_WORKFLOW);
            foreach ($this->configuration->getProcessors() as $processor) {
                $processor->setLogger($this->logger);
            }
            $writers = $this->configuration->getWriters();
            foreach ($writers as $index => $writer) {
                if (isset($this->writerResults[$index])) {
                    $writer->setResults($this->writerResults[$index]);
                }
                $writer->prepare();
            }

            $reader = $this->configuration->getReader();
            $itemsIterator = ($reader)();
            $this->totalItemsCount = $itemsIterator->getTotalCount();

            $this->dispatcher->dispatch(new WorkflowEvent($this), WorkflowEvent::START);

            $limitIterator = new LimitIterator($itemsIterator, $this->offset, $batchLimit);
            foreach ($limitIterator as $index => $item) {
                $this->logger->setItemIndex($index);
                $this->referenceBag->resetScope(Reference::SCOPE_ITEM);
                try {
                    foreach ($this->configuration->getProcessors() as $processor) {
                        $processResult = ($processor)($item);
                        if (false === $processResult) {
                            continue 2;
                        }
                        if (null !== $processResult) {
                            $item = $processResult;
                        }
                    }
                } catch (Throwable $e) {
                    if ($this->debug) {
                        throw $e;
                    }
                    $this->logger->logException($e);
                }
                ++$this->offset;
                $this->dispatcher->dispatch(new WorkflowEvent($this), WorkflowEvent::PROGRESS);
            }

            foreach ($writers as $index => $writer) {
                $writer->finish();
                $this->writerResults[$index] = $writer->getResults();
            }
        } catch (Throwable $e) {
            if ($this->debug) {
                throw $e;
            }
            $this->logger->setItemIndex(null);
            $this->logger->logException($e);
        }

        $this->endTime = new DateTimeImmutable();
        $this->finish();
    }

    public function clean(): void
    {
        $this->configuration->getReader()->clean();
        foreach ($this->configuration->getProcessors() as $processor) {
            $processor->clean();
        }
    }

    public function setLogger(WorkflowLoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    abstract public static function getDefaultConfig(): WorkflowConfiguration;

    public static function getConfigurationFormType(): ?string
    {
        return WorkflowProcessConfigurationFormType::class;
    }

    public function getStartTime(): DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getEndTime(): DateTimeImmutable
    {
        return $this->endTime;
    }

    public function getWriterResults(): array
    {
        return $this->writerResults;
    }

    public function setWriterResults(array $writerResults): void
    {
        $this->writerResults = $writerResults;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    public function getTotalItemsCount(): int
    {
        return $this->totalItemsCount;
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }
}

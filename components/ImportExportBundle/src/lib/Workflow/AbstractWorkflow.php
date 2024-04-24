<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow;

use AlmaviaCX\Bundle\IbexaImportExport\Event\BasicEventDispatcherTrait;
use AlmaviaCX\Bundle\IbexaImportExport\Monolog\WorkflowLoggerInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Reference\Reference;
use AlmaviaCX\Bundle\IbexaImportExport\Reference\ReferenceBag;
use DateTimeImmutable;
use Iterator;
use LimitIterator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Throwable;

abstract class AbstractWorkflow implements WorkflowInterface
{
    use BasicEventDispatcherTrait;

    protected Iterator $itemsIterator;
    protected WorkflowLoggerInterface $logger;
    protected WorkflowExecutionConfiguration $configuration;
    protected EventDispatcherInterface $dispatcher;

    protected ReferenceBag $referenceBag;
    protected DateTimeImmutable $startTime;
    protected DateTimeImmutable $endTime;
    protected array $writerResults = [];
    protected ?int $totalItemsCount = null;
    protected int $offset = 0;
    protected float $progress = 0;
    protected bool $debug = false;

    public function __construct(ReferenceBag $references)
    {
        $this->referenceBag = $references;
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
        $this->startTime = new DateTimeImmutable();
        $this->referenceBag->resetScope(Reference::SCOPE_WORKFLOW);
        foreach ($this->configuration->getProcessors() as $processor) {
            $processor->setLogger($this->logger);
            $processor->prepare();
        }
        foreach ($this->configuration->getWriters() as $index => $writer) {
            if (isset($this->writerResults[$index])) {
                $writer->setResults($this->writerResults[$index]);
            }
        }
        $reader = $this->configuration->getReader();
        $reader->prepare();
        $this->itemsIterator = ($reader)();
        if (!$this->totalItemsCount) {
            $this->totalItemsCount = $this->itemsIterator->count();
        }

        $this->dispatchEvent(new WorkflowEvent($this), WorkflowEvent::PREPARE);
    }

    protected function finish(): void
    {
        $this->endTime = new DateTimeImmutable();
        foreach ($this->configuration->getProcessors() as $processor) {
            $processor->finish();
        }
        foreach ($this->configuration->getWriters() as $index => $writer) {
            $this->writerResults[$index] = $writer->getResults();
        }
        $this->configuration->getReader()->finish();

        $this->dispatchEvent(new WorkflowEvent($this), WorkflowEvent::FINISH);
    }

    public function __invoke(int $batchLimit = -1): void
    {
        $this->prepare();

        try {
            $this->dispatchEvent(new WorkflowEvent($this), WorkflowEvent::START);

            $limitIterator = new LimitIterator($this->itemsIterator, $this->offset, $batchLimit);
            foreach ($limitIterator as $index => $item) {
                $this->logger->setItemIndex($index + 1);
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
                $this->dispatchEvent(new WorkflowEvent($this), WorkflowEvent::PROGRESS);
            }
        } catch (Throwable $e) {
            if ($this->debug) {
                throw $e;
            }
            $this->logger->setItemIndex(null);
            $this->logger->logException($e);
        }

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

    abstract public function getDefaultConfig(): WorkflowConfiguration;

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

    public function setTotalItemsCount(?int $totalItemsCount): void
    {
        $this->totalItemsCount = $totalItemsCount;
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }
}

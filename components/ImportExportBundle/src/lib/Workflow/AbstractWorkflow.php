<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow;

use AlmaviaCX\Bundle\IbexaImportExport\Event\BasicEventDispatcherTrait;
use AlmaviaCX\Bundle\IbexaImportExport\Exception\BaseException;
use AlmaviaCX\Bundle\IbexaImportExport\File\TempFileUtil;
use AlmaviaCX\Bundle\IbexaImportExport\Item\ItemAccessorInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Monolog\WorkflowLogger;
use AlmaviaCX\Bundle\IbexaImportExport\Monolog\WorkflowLoggerInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderIteratorInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Reference\Reference;
use DateTimeImmutable;
use LimitIterator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Throwable;

/**
 * @phpstan-import-type ProcessableItem from \AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface
 */
abstract class AbstractWorkflow implements WorkflowInterface
{
    use BasicEventDispatcherTrait;

    /**
     * @var \AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderIteratorInterface<mixed, ItemAccessorInterface>
     */
    protected ReaderIteratorInterface $itemsIterator;
    protected ?WorkflowLoggerInterface $logger = null;
    protected WorkflowExecutionConfiguration $configuration;
    protected EventDispatcherInterface $dispatcher;
    protected WorkflowState $state;
    protected bool $debug = false;

    /**
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowExecutionConfiguration $configuration
     */
    public function setConfiguration(WorkflowExecutionConfiguration $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function setState(WorkflowState $state): void
    {
        $this->state = $state;
    }

    /**
     * @return \AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowState
     */
    public function getState(): WorkflowState
    {
        return $this->state;
    }

    protected function prepare(): void
    {
        if (!$this->logger) {
            $this->logger = new WorkflowLogger();
        }

        foreach ($this->configuration->getProcessors() as $processorIdentifier => $processor) {
            $processor->setIdentifier($processorIdentifier);
            $processor->setLogger($this->logger);
            $processor->setState($this->state);
            $processor->prepare();
        }
        $reader = $this->configuration->getReader();
        $reader->setLogger($this->logger);
        $reader->setState($this->state);
        $reader->prepare();
        $this->itemsIterator = ($reader)();

        if (0 === $this->state->getOffset()) {
            $this->state->setStartTime(new DateTimeImmutable());
            $this->state->setTotalItemsCount($this->itemsIterator->count());
        }

        $this->dispatchEvent(new WorkflowEvent($this), WorkflowEvent::PREPARE);
    }

    protected function finish(): void
    {
        TempFileUtil::removeTempFiles();
        foreach ($this->configuration->getProcessors() as $processor) {
            $processor->finish();
        }
        $this->configuration->getReader()->finish();

        if ($this->state->isCompleted()) {
            $this->state->setEndTime(new DateTimeImmutable());
            $this->state->getCache()->clear();
        }

        $this->dispatchEvent(new WorkflowEvent($this), WorkflowEvent::FINISH);
    }

    public function __invoke(int $batchLimit = -1): void
    {
        try {
            $this->prepare();
            $workflowEvent = new WorkflowEvent($this);
            $this->dispatchEvent($workflowEvent, WorkflowEvent::START);

            $limitIterator = new LimitIterator(
                $this->itemsIterator,
                $this->state->getOffset(),
                $batchLimit
            );

            foreach ($limitIterator as $index => $item) {
                $this->logger->setItemIndex($index + 1);
                $this->state->getReferenceBag()->resetScope(Reference::SCOPE_ITEM);
                $this->processItem($item);
                $this->state->setOffset($this->state->getOffset() + 1);
                $this->dispatchEvent($workflowEvent, WorkflowEvent::PROGRESS);
                if (!$workflowEvent->canContinue()) {
                    break;
                }
            }
        } catch (Throwable $e) {
            $this->logger->setItemIndex(null);
            $this->logger->logException($e);
            throw $e;
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

    public function getLogger(): ?WorkflowLoggerInterface
    {
        return $this->logger;
    }

    abstract public function getDefaultConfig(): WorkflowConfiguration;

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    /**
     * @param ProcessableItem $item
     *
     * @throws \Throwable
     */
    protected function processItem($item): void
    {
        try {
            $processorId = null;
            foreach ($this->configuration->getProcessors() as $processorId => $processor) {
                $processResult = ($processor)($item);
                if (false === $processResult) {
                    return;
                }
                if (null !== $processResult) {
                    $item = $processResult;
                }
            }
        } catch (Throwable $procesItemException) {
            $exception = new BaseException(
                sprintf('[%s] %s', $processorId, $procesItemException->getMessage()),
                $procesItemException->getCode(),
                $procesItemException
            );

            if ($this->debug) {
                throw $exception;
            }
            $this->logger->logException($exception);
        }
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }
}

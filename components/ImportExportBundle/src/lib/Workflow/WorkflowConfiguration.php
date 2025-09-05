<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentReference;
use AlmaviaCX\Bundle\IbexaImportExport\Item\ItemAccessorInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Step\Callback\CallbackStep;
use AlmaviaCX\Bundle\IbexaImportExport\Step\Callback\CallbackStepOptions;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 *
 * @ORM\Table(name="import_export_workflow_configuration")
 */
class WorkflowConfiguration
{
    public const AVAILABILITY_CLI = 1;
    public const AVAILABILITY_ADMIN_UI = 2;

    /**
     * @ORM\Id
     *
     * @ORM\Column(type="string")
     */
    protected string $identifier;

    /**
     * @ORM\Column
     */
    protected string $name;

    /**
     * @ORM\Column
     */
    protected WorkflowProcessConfiguration $processConfiguration;

    protected int $availability = self::AVAILABILITY_CLI | self::AVAILABILITY_ADMIN_UI;

    public function __construct(
        string $identifier,
        string $name,
        int $availability = self::AVAILABILITY_CLI | self::AVAILABILITY_ADMIN_UI
    ) {
        $this->availability = $availability;
        $this->identifier = $identifier;
        $this->name = $name;
        $this->processConfiguration = new WorkflowProcessConfiguration();
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getProcessConfiguration(): WorkflowProcessConfiguration
    {
        return $this->processConfiguration;
    }

    public function setProcessConfiguration(WorkflowProcessConfiguration $processConfiguration): void
    {
        $this->processConfiguration = $processConfiguration;
    }

    public function setReader(string $class, ?ReaderOptions $options = null): void
    {
        $requiredOptionsType = call_user_func([$class, 'getOptionsType']);
        if (!$options) {
            $options = new $requiredOptionsType();
        }
        if (!$options instanceof $requiredOptionsType) {
            throw new \InvalidArgumentException('Options must be an instance of '.$requiredOptionsType);
        }
        $this->processConfiguration->setReader(new ComponentReference($class, $options));
    }

    /**
     * @param callable(ItemAccessorInterface $item): ?ItemAccessorInterface $callback
     */
    public function addCallbackProcessor(callable $callback): void
    {
        $option = new CallbackStepOptions();
        $option->callback = $callback;
        $this->addProcessor(CallbackStep::class, $option);
    }

    public function addProcessor(string $class, ?ProcessorOptions $options = null): void
    {
        $requiredOptionsType = call_user_func([$class, 'getOptionsType']);
        if (!$options) {
            $options = new $requiredOptionsType();
        }
        if (!$options instanceof $requiredOptionsType) {
            throw new \InvalidArgumentException('Options must be an instance of '.$requiredOptionsType);
        }
        $this->processConfiguration->addProcessor(new ComponentReference($class, $options));
    }

    public function getReader(): ComponentReference
    {
        return $this->processConfiguration->getReader();
    }

    /**
     * @return array<ComponentReference>
     */
    public function getProcessors(): array
    {
        return $this->processConfiguration->getProcessors();
    }

    /**
     * @return int
     */
    public function isAvailable(int $requiredAvailability): bool
    {
        return $requiredAvailability === ($requiredAvailability & $this->availability);
    }
}

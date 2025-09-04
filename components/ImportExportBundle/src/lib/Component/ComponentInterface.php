<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Component;

use AlmaviaCX\Bundle\IbexaImportExport\Monolog\WorkflowLoggerInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowState;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @template TOptions of ComponentOptions
 */
interface ComponentInterface
{
    public static function getName(): TranslatableMessage|string;

    public static function getOptionsFormType(): ?string;

    /**
     * @return class-string<TOptions>
     */
    public static function getOptionsType(): string;

    /**
     * @param TOptions $options
     */
    public function setOptions($options): void;

    /**
     * @return TOptions
     */
    public function getOptions();

    public function clean(): void;

    public function setLogger(WorkflowLoggerInterface $logger): void;

    public function prepare(): void;

    public function finish(): void;

    public function setState(WorkflowState $state): void;
}

<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Component;

use AlmaviaCX\Bundle\IbexaImportExport\Monolog\WorkflowLoggerInterface;
use Symfony\Component\Translation\TranslatableMessage;

interface ComponentInterface
{
    /**
     * Component name.
     *
     * @return string|TranslatableMessage
     */
    public static function getName();

    public static function getOptionsFormType(): ?string;

    public static function getOptionsType(): ?string;

    public function setOptions(ComponentOptions $options): void;

    public function getOptions(): ComponentOptions;

    public function clean(): void;

    public function setLogger(WorkflowLoggerInterface $logger): void;

    public function prepare(): void;

    public function finish(): void;
}

<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Component;

use Psr\Log\LoggerAwareInterface;
use Symfony\Component\Translation\TranslatableMessage;

interface ComponentInterface extends LoggerAwareInterface
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
}

<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\CaptchEtat\Value;

use Symfony\Component\VarExporter\LazyGhostTrait;

class CaptchEtatChallenge
{
    use LazyGhostTrait;

    public ?string $captchaHtml;
    public ?string $captchaId;

    public function __construct(
        ?string $captchaHtml,
        ?string $captchaId
    ) {
        $this->captchaId = $captchaId;
        $this->captchaHtml = $captchaHtml;
    }
}

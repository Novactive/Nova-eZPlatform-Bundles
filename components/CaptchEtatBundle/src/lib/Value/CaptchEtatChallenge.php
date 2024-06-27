<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\CaptchEtat\Value;

class CaptchEtatChallenge
{
    private string $captchaHtml;
    private string $captchaId;

    public function __construct(
        string $captchaHtml,
        string $captchaId
    ) {
        $this->captchaId = $captchaId;
        $this->captchaHtml = $captchaHtml;
    }

    public function getCaptchaHtml(): string
    {
        return $this->captchaHtml;
    }

    public function getCaptchaId(): string
    {
        return $this->captchaId;
    }
}

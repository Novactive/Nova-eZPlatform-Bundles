<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\CaptchEtat\Challenge;

use AlmaviaCX\Bundle\CaptchEtat\Api\Gateway;

class ChallengeValidator
{
    protected Gateway $gateway;

    public function __construct(
        Gateway $gateway
    ) {
        $this->gateway = $gateway;
    }

    public function isValid(string $captchaId, string $answer): bool
    {
        return $this->gateway->validateChallenge($captchaId, $answer);
    }
}

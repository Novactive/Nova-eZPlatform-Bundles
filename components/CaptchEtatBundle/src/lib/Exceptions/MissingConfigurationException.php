<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\CaptchEtat\Exceptions;

use InvalidArgumentException;

class MissingConfigurationException extends InvalidArgumentException
{
    protected $message = 'Missing CAPTCHETAT_OAUTH_CLIENT_ID or CAPTCHETAT_OAUTH_CLIENT_SECRET.';
}

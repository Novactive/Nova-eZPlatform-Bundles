<?php

declare(strict_types=1);

namespace Novactive\EzMenuManager\Exception;

use Exception;
use Throwable;

class MenuNotFoundException extends Exception
{
    public function __construct($identifier, Throwable $previous = null)
    {
        $message = sprintf("Could not find menu with identifier '%s'", $identifier);
        parent::__construct($message, 404, $previous);
    }
}

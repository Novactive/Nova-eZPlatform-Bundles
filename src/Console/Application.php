<?php

/**
 * eZ Platform Bundles Mono Repo Project.
 *
 * @author    Novactive - Sébastien Morel <s.morel@novactive.com> aka Plopix <morel.seb@gmail.com>
 * @copyright 2020 Novactive
 * @license   MIT
 */

declare(strict_types=1);

namespace Novactive\eZPlatform\Bundles\Console;

use Symfony\Component\Console\Application as BaseApplication;

final class Application extends BaseApplication
{
    private static string $logo = '
        |-- <fg=yellow;options=bold>eZ-Platform-Bundles</>
        |    |-- components 
        |    |    |-- <fg=green;options=bold>bundles</><fg=blue>1</>
        |    |    |-- <fg=green;options=bold>bundles</><fg=blue>2</>
        |    |    |-- <fg=green;options=bold>bundles</><fg=blue>3</>
        |    |    |-- <fg=green;options=bold>bundles</><fg=blue>N</>
        
        ';

    #[\Override]
    public function getHelp(): string
    {
        return self::$logo.parent::getHelp();
    }

    public function getLogo(): string
    {
        return self::$logo.$this->getLongVersion();
    }
}

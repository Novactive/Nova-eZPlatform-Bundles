<?php

/**
 * eZ Platform Bundles Mono Repo Project.
 *
 * @author    Novactive - Sébastien Morel <s.morel@novactive.com> aka Plopix <morel.seb@gmail.com>
 * @copyright 2020 Novactive
 * @license   MIT
 */

declare(strict_types=1);

namespace Novactive\eZPlatform\Bundles\Core;

use Symfony\Component\Process\Process;

class Runner
{
    public function __invoke(array $commands)
    {
        foreach ($commands as $command) {
            $process = new Process($command);
            $process->setTimeout(7200);
            $process->setIdleTimeout(1800);
            $process->setTty(true);
            $process->mustRun();
        }
    }
}

<?php

/**
 * eZ Platform Bundles Mono Repo Project.
 *
 * @author    Novactive - Sébastien Morel <s.morel@novactive.com> aka Plopix <morel.seb@gmail.com>
 * @copyright 2020 Novactive
 * @license   MIT
 */

declare(strict_types=1);

namespace Novactive\eZPlatform\Bundles\Core\Collection;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class Branches
{
    public function __invoke(): array
    {
        $process = Process::fromShellCommandline("git branch -a | grep 'remotes/origin/.*'  | awk '{print $1}'");
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $branches = [];
        foreach (explode("\n", $process->getOutput()) as $branchName) {
            if (preg_match('#^remotes/origin/(?!HEAD)(.*)$#', $branchName, $matches)) {
                $branches[] = $matches[1];
            }
        }

        return $branches;
    }
}

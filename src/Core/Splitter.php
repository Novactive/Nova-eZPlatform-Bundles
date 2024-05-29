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

final class Splitter
{
    public function __invoke(Component $component, string $branch)
    {
        $localBranchName = uniqid("{$component}-{$branch}-", false);
        $commands = [
            ['git', 'fetch', 'origin'],
            [
                'splitsh-lite', "--prefix=components/{$component}", "--origin=origin/{$branch}",
                "--target=refs/heads/{$localBranchName}",
            ],
            ['git', 'remote', 'add', $component, "git@github.com:{$component->getRepo()}.git"],
            ['git', 'push', '-f', $component, "refs/heads/{$localBranchName}:refs/heads/{$branch}"],
            ['git', 'remote', 'rm', $component],
            ['git', 'update-ref', '-d', "refs/heads/{$localBranchName}"],
        ];

        (new Process\Runner())($commands);
    }
}

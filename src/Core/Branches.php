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

use Symfony\Component\Finder\Finder;

final class Branches
{
    public function __invoke(): array
    {
        $branches = [];
        $finder = new Finder();
        $finder->depth(0)->files()->in('.git/refs/heads');
        foreach ($finder as $file) {
            $branches[] = $file->getBasename();
        }

        return $branches;
    }
}

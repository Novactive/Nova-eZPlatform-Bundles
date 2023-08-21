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

use Novactive\eZPlatform\Bundles\Core\Component;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

final class Components
{
    public function __invoke(): array
    {
        $components = [];
        $finder = new Finder();
        $finder->directories()->in(__DIR__.'/../../../components')->ignoreUnreadableDirs()->depth(0);
        foreach ($finder as $component) {
            $configPath = $component->getRealPath().'/ci-config.yaml';
            $config = file_exists($configPath) ? Yaml::parseFile($configPath) : null;
            $components[$component->getBasename()] = new Component(
                $component->getBasename(),
                $config['repo'] ?? null
            );
        }
        asort($components);

        return $components;
    }
}

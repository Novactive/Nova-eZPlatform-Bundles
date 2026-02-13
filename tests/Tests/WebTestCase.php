<?php

/**
 * eZ Platform Bundles Mono Repo Project.
 *
 * @author    Novactive - Sébastien Morel <s.morel@novactive.com> aka Plopix <morel.seb@gmail.com>
 * @copyright 2020 Novactive
 * @license   MIT
 */

declare(strict_types=1);

namespace Novactive\eZPlatform\Bundles\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseTestCase;

class WebTestCase extends BaseTestCase
{
    protected function get(string $serviceId)
    {
        return self::getContainer()->get($serviceId);
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }
}

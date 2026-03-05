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

use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\Panther\Client as PantherClient;
use Symfony\Component\Panther\PantherTestCaseTrait;

class PantherTestCase extends BaseTestCase
{
    use PantherTestCaseTrait;

    public const CHROME = 'chrome';
    public const FIREFOX = 'firefox';
    public const BASE_URI = 'https://127.0.0.1:8000';

    protected function getPantherClient(): PantherClient
    {
        return self::createPantherClient(
            [
                'browser' => self::FIREFOX,
                'external_base_uri' => getenv('PANTHER_EXTERNAL_BASE_URI') ? :
                    $_SERVER['PANTHER_EXTERNAL_BASE_URI'] ?? self::BASE_URI,
            ],
            [],
            [
                'capabilities' => [
                    'acceptInsecureCerts' => true,
                ]
            ]
        );
    }
}

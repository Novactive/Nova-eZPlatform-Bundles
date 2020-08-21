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

use Novactive\eZPlatform\Bundles\Tests\Contracts\LoginPanther;

final class LoginPantherTest extends PantherTestCase
{
    use LoginPanther;

    public function testAdminLogin(): void
    {
        $helper = new BrowserHelper($this->getPantherClient());
        $this->logAdmin($helper);
    }
}

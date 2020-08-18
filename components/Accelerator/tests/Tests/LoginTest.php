<?php

/**
 * Nova eZ Accelerator.
 *
 * @package   Novactive\Bundle\eZAccelerator
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @author    Sébastien Morel (Plopix) <morel.seb@gmail.com>
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZAccelerator/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAccelerator\Tests;

final class LoginTest extends TestCase
{
    public function testAdminLogin(): void
    {
        $helper = new BrowserHelper($this->getPantherClient());
        $crawler = $helper->get('/admin/login');

        $this->assertStringContainsString('ez-login__form-wrapper', $helper->client()->getPageSource());

        $form = $crawler->filter('.ez-login__form-wrapper form');
        $form->form(
            [
                '_username' => 'admin',
                '_password' => 'publish',
            ]
        );
        $form->submit();

        $tab = '.nav.nav-tabs .nav-item.last';
        $crawler = $helper->waitFor($tab);
        $crawler->filter($tab)->count();
        $this->assertEquals(1, $crawler->filter($tab)->count());
    }
}

<?php

/**
 * eZ Platform Bundles Mono Repo Project.
 *
 * @author    Novactive - Sébastien Morel <s.morel@novactive.com> aka Plopix <morel.seb@gmail.com>
 * @copyright 2020 Novactive
 * @license   MIT
 */

declare(strict_types=1);

namespace Novactive\eZPlatform\Bundles\Tests\Contracts;

use Novactive\eZPlatform\Bundles\Tests\BrowserHelper;

trait LoginPanther
{
    public function logAdmin(BrowserHelper $helper): void
    {
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

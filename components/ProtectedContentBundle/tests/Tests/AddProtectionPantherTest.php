<?php

/**
 * eZ Platform Bundles Mono Repo Project.
 *
 * @author    Novactive - Sébastien Morel <s.morel@novactive.com> aka Plopix <morel.seb@gmail.com>
 * @copyright 2020 Novactive
 * @license   MIT
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZProtectedContentBundle\Tests;

use Novactive\eZPlatform\Bundles\Tests\BrowserHelper;
use Novactive\eZPlatform\Bundles\Tests\Contracts\LoginPanther;
use Novactive\eZPlatform\Bundles\Tests\PantherTestCase;

class AddProtectionPantherTest extends PantherTestCase
{
    use LoginPanther;

    public function testAddProtection(): void
    {
        $helper = new BrowserHelper($this->getPantherClient());
        $this->logAdmin($helper);

        $helper->get('/admin/future-protected-article');

        $crawler = $helper->waitFor('#ez-tab-location-view-protect-content');
        $crawler->filter('a[href="#ez-tab-location-view-protect-content"]')->click();
        $crawler->filter('button[data-target="#ez-modal--add-content-protection"]')->click();
        $helper->wait(1);
        $form = $crawler->filter('form[name="protected_access"]');
        $this->assertEquals(1, $form->count());
        $form->form(
            [
                'protected_access[password]' => 'plopix',
                'protected_access[protectChildren]' => 0,
                'protected_access[enabled]' => 1,
            ]
        );
        $form->submit();
        $crawler = $helper->waitFor('#ez-tab-location-view-protect-content');
        $count = $crawler->filter('#ez-tab-location-view-protect-content table.ez-table.table > tbody > tr')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function testSeeProtection(): void
    {
        $helper = new BrowserHelper($this->getPantherClient());
        $helper->get('/future-protected-article');
        $source = $helper->client()->getPageSource();
        $this->assertStringContainsString('This content has been protected by a password', $source);
    }

    public function testSeeAfterUnlockProtection(): void
    {
        $helper = new BrowserHelper($this->getPantherClient());
        $crawler = $helper->get('/future-protected-article');
        $form = $crawler->filter('form[name="request_protected_access"]');
        $form->form(
            [
                'request_protected_access[password]' => 'plopix',
            ]
        );
        $form->submit();
        $helper->waitFor('body');
        $source = $helper->client()->getPageSource();
        $this->assertStringContainsString('4786jsg6723', $source);
    }
}

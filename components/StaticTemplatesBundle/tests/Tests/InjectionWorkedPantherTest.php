<?php

/**
 * eZ Platform Bundles Mono Repo Project.
 *
 * @author    Novactive - Sébastien Morel <s.morel@novactive.com> aka Plopix <morel.seb@gmail.com>
 * @copyright 2020 Novactive
 * @license   MIT
 */

declare(strict_types=1);

namespace Novactive\Bundle\EzStaticTemplatesBundle\Tests;

use Novactive\eZPlatform\Bundles\Tests\BrowserHelper;
use Novactive\eZPlatform\Bundles\Tests\PantherTestCase;

class InjectionWorkedPantherTest extends PantherTestCase
{
    public function testInjectionWorked(): void
    {
        $helper = new BrowserHelper($this->getPantherClient());
        $crawler = $helper->get('/static-ultimatenova/legrasclavie');
        $form = $crawler->filter('form');
        $form->form(
            [
                '_username' => 'admin',
                '_password' => 'publish',
            ]
        );
        $form->submit();

        $helper->get('/static-ultimatenova/legrasclavie');

        $source = $helper->client()->getPageSource();

        $this->assertStringContainsString('I am a static Template loaded via a dynamic SiteAccess!', $source);
    }
}

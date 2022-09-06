<?php

/**
 * eZ Platform Bundles Mono Repo Project.
 *
 * @author    Novactive - Sébastien Morel <s.morel@novactive.com> aka Plopix <morel.seb@gmail.com>
 * @copyright 2020 Novactive
 * @license   MIT
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZExtraBundle\Tests;

use Ibexa\Contracts\Core\Repository\Repository;
use Faker\Factory;
use Novactive\Bundle\eZExtraBundle\Core\Manager\eZ\Content as ContentManager;
use Novactive\eZPlatform\Bundles\Tests\WebTestCase;

class ContentManagerTest extends WebTestCase
{
    /**
     * @var ContentManager
     */
    private $manager;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var Repository $repo */
        $repo = $this->get(Repository::class);
        $repo->getPermissionResolver()->setCurrentUserReference($repo->getUserService()->loadUser(14));
        $this->manager = $this->get(ContentManager::class);
    }

    private function wrapRichText(string $text): string
    {
        return trim(
            '<?xml version="1.0" encoding="UTF-8"?>
                <section 
                    xmlns="http://docbook.org/ns/docbook" 
                    xmlns:xlink="http://www.w3.org/1999/xlink" 
                    xmlns:ezxhtml="http://ez.no/xmlns/ezpublish/docbook/xhtml" 
                    xmlns:ezcustom="http://ez.no/xmlns/ezpublish/docbook/custom" 
                    version="5.0-variant ezpublish-1.0"
                >
                    <para>'.$text.'</para>
            </section>'
        );
    }

    public function testContentCreation(): void
    {
        $faker = Factory::create();
        $title = $faker->streetName;
        $subTitle = $faker->city;
        $content = $this->manager->createContent(
            'article',
            2,
            [
                'title' => $title,
                'short_title' => $subTitle,
                'intro' => $this->wrapRichText('Intro Plop!'),
                'body' => $this->wrapRichText('Body Plop!'),
                'enable_comments' => true,
            ]
        );
        $this->assertEquals($subTitle, $content->contentInfo->name);
    }
}

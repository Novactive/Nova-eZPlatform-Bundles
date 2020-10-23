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

use eZ\Publish\API\Repository\Repository;

class RepoIntegrationTest extends WebTestCase
{
    public function testRepoLogin(): void
    {
        /** @var Repository $repo */
        $repo = $this->get(Repository::class);

        $location = $repo->getLocationService()->loadLocation(2);

        $this->assertEquals(2, $location->id);
        $this->assertEquals("Ibexa Platform", $location->contentInfo->name);
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

    public function testCreateArticle(): void
    {
        /** @var Repository $repository */
        $repository = $this->get(Repository::class);
        $repository->getPermissionResolver()->setCurrentUserReference($repository->getUserService()->loadUser(14));

        $location = $repository->getLocationService()->loadLocation(2);

        $this->assertEquals(2, $location->id);
        $this->assertEquals("Ibexa Platform", $location->contentInfo->name);

        $contentService = $repository->getContentService();
        $locationService = $repository->getLocationService();
        $contentTypeService = $repository->getContentTypeService();

        $title = "An article that will be Protected later.";
        $shortTitle = "Future Protected Article";
        $intro = $this->wrapRichText("Intro");
        $body = $this->wrapRichText("The coupon code is: 4786jsg6723");

        $contentType = $contentTypeService->loadContentTypeByIdentifier('article');
        $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-GB');
        $contentCreateStruct->setField('title', $title);
        $contentCreateStruct->setField('short_title', $shortTitle);
        $contentCreateStruct->setField('intro', $intro);
        $contentCreateStruct->setField('body', $body);

        // instantiate a location create struct from the parent location
        $locationCreateStruct = $locationService->newLocationCreateStruct($location->id);

        // create a draft using the content and location create struct and publish it
        $draft = $contentService->createContent($contentCreateStruct, [$locationCreateStruct]);
        $content = $contentService->publishVersion($draft->versionInfo);
        $this->assertEquals("Future Protected Article", $content->contentInfo->name);
    }
}

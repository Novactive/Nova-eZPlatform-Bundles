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
        $this->assertEquals("eZ Platform", $location->contentInfo->name);
    }
}

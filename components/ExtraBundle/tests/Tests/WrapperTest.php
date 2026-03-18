<?php

/**
 * NovaeZExtraBundle Bundle.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZExtraBundle\Tests;

use Ibexa\Contracts\Core\Repository\Repository;
use Novactive\Bundle\eZExtraBundle\Core\Helper\eZ\WrapperFactory;
use Novactive\eZPlatform\Bundles\Tests\WebTestCase;

class WrapperTest extends WebTestCase
{
    /**
     * @var WrapperFactory
     */
    private $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->get(WrapperFactory::class);
    }

    public function testByLocationId(): void
    {
        $wrapper = $this->factory->createByLocationId(2);
        $this->assertEquals(2, $wrapper->location->id);
        $this->assertEquals(1, $wrapper->content->id);
        $this->assertEquals('folder', $wrapper->content->getContentType()->identifier);
    }

    public function testByContentId(): void
    {
        $wrapper = $this->factory->createByContentId(1);
        $this->assertEquals(2, $wrapper->location->id);
        $this->assertEquals(1, $wrapper->content->id);
        $this->assertEquals('folder', $wrapper->content->getContentType()->identifier);
    }

    public function testByLocation(): void
    {
        /** @var Repository $repo */
        $repo = $this->get(Repository::class);
        $location = $repo->getLocationService()->loadLocation(2);
        $wrapper = $this->factory->createByLocation($location);
        $this->assertEquals(2, $wrapper->location->id);
        $this->assertEquals(1, $wrapper->content->id);
        $this->assertEquals('folder', $wrapper->content->getContentType()->identifier);
    }

    public function testByContent(): void
    {
        /** @var Repository $repo */
        $repo = $this->get(Repository::class);
        $content = $repo->getContentService()->loadContent(1);
        $wrapper = $this->factory->createByContent($content);
        $this->assertEquals(2, $wrapper->location->id);
        $this->assertEquals(1, $wrapper->content->id);
        $this->assertEquals('folder', $wrapper->content->getContentType()->identifier);
    }
}

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
use Novactive\Bundle\eZExtraBundle\Core\Helper\eZ\Content as ContentHelper;
use Novactive\Bundle\eZExtraBundle\Core\Helper\eZ\Wrapper;
use Novactive\eZPlatform\Bundles\Tests\WebTestCase;

class ContentHelperTest extends WebTestCase
{
    /**
     * @var ContentHelper
     */
    private $helper;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var Repository $repo */
        $repo = $this->get(Repository::class);
        $repo->getPermissionResolver()->setCurrentUserReference($repo->getUserService()->loadUser(14));
        $this->helper = $this->get(ContentHelper::class);
    }

    public function testDefaultUserGroupList(): void
    {
        $list = $this->helper->contentList(5, ['user']);
        $this->assertCount(0, $list);

        $list = $this->helper->contentList(4, ['user_group']);
        $this->assertCount(0, $list);

        foreach ($list as $wrapper) {
            $this->assertInstanceOf(Wrapper::class, $wrapper);
        }
    }

    public function testDefaultUserTree(): void
    {
        $list = $this->helper->contentTree(5, ['user']);
        $this->assertCount(2, $list);
        foreach ($list as $wrapper) {
            $this->assertInstanceOf(Wrapper::class, $wrapper);
        }
    }
}

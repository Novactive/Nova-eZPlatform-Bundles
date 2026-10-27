<?php

declare(strict_types=1);

namespace Novactive\Bundle\eZProtectedContentBundle\Event;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Symfony\Contracts\EventDispatcher\Event;

final class BeforeProtectContentEvent extends Event
{
    public function __construct(
        public readonly ContentInfo $contentInfo,
    ) {
    }
}

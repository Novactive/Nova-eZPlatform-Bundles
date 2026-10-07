<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaSiteFactoryExtra\Message;

class CreateUserGroup
{
    public function __construct(
        public int $siteId,
        public int $creatorId
    ) {
    }
}

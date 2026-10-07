<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaSiteFactoryExtra\Message;

class UpdatePublicAccesses
{
    public function __construct(
        public int $siteId,
        public bool $isCreate = false
    ) {
    }
}

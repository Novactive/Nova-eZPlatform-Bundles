<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaMigrationExtra\Migration\ValueObject\Step;

use Ibexa\Migration\ValueObject\Location\Matcher;
use Ibexa\Migration\ValueObject\Step\StepInterface;

class LocationMoveStep implements StepInterface
{
    public Matcher $locationMatch;

    public Matcher $newParentLocationMatch;

    public function __construct(Matcher $locationMatch, Matcher $newParentLocationMatch)
    {
        $this->locationMatch = $locationMatch;
        $this->newParentLocationMatch = $newParentLocationMatch;
    }
}

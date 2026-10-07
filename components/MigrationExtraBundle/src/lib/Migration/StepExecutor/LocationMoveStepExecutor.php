<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaMigrationExtra\Migration\StepExecutor;

use AlmaviaCX\Bundle\IbexaMigrationExtra\Migration\ValueObject\Step\LocationMoveStep;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Migration\Generator\Exception\UnknownMatchPropertyException;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\StepExecutor\StepExecutorInterface;
use Ibexa\Migration\ValueObject\Location\Matcher;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Webmozart\Assert\Assert;

class LocationMoveStepExecutor extends AbstractStepExecutor implements StepExecutorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private LocationService $locationService;

    public function __construct(
        LocationService $locationService,
        ?LoggerInterface $logger = null
    ) {
        $this->locationService = $locationService;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof LocationMoveStep;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function getLocationByMatch(Matcher $match): Location
    {
        if (Matcher::LOCATION_REMOTE_ID === $match->field) {
            return $this->locationService->loadLocationByRemoteId((string) $match->value);
        } elseif (Matcher::LOCATION_ID === $match->field) {
            Assert::integerish($match->value);

            return $this->locationService->loadLocation((int) $match->value);
        }

        throw new UnknownMatchPropertyException($match->field, [Matcher::LOCATION_ID, Matcher::LOCATION_REMOTE_ID]);
    }

    /**
     * @param LocationMoveStep $step
     *
     * @phpstan-return array{
     *      Location,
     *      Location,
     *  }
     */
    protected function doHandle(StepInterface $step): array
    {
        Assert::isInstanceOf($step, LocationMoveStep::class);

        $location = $this->getLocationByMatch($step->locationMatch);
        $newParentLocation = $this->getLocationByMatch($step->newParentLocationMatch);

        $this->locationService->moveSubtree($location, $newParentLocation);

        $content1 = $location->getContent();
        $content2 = $newParentLocation->getContent();

        $this->getLogger()->notice(
            sprintf(
                'Moved location. Location for content: "%s" (ID: %s, Location ID: %s) 
                moved to: "%s" (ID: %s, Location ID: %s)',
                $content1->getName(),
                $content1->id,
                $location->id,
                $content2->getName(),
                $content2->id,
                $newParentLocation->id,
            )
        );

        return [$location, $newParentLocation];
    }
}

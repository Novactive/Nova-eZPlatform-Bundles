<?php

declare(strict_types=1);

namespace AlmaviaCX\Ibexa\Commerce\Extra\Service\SiteAccessAware\Cart\Handler;

use AlmaviaCX\Ibexa\Commerce\Extra\Service\SiteAccessAware\Cart\NamedCartService;
use Ibexa\Cart\Persistence\Legacy\Cart\GatewayInterface;
use Ibexa\Cart\Persistence\Legacy\Cart\Handler\DatabaseHandler as BaseDatabaseHandler;
use Ibexa\Cart\Persistence\Legacy\Cart\Handler\HandlerInterface;
use Ibexa\Contracts\Cart\Value\CartQuery;

final class DatabaseHandler extends NameCartHandler implements HandlerInterface
{
    public function __construct(
        private readonly GatewayInterface $gateway,
        NamedCartService $namedCartService,
        BaseDatabaseHandler $innerHandler
    ) {
        parent::__construct($namedCartService, $innerHandler);
    }

    public function countMatching(CartQuery $query): int
    {
        $criteria = [];
        $criteria['name'] = $this->namedCartService->getNamedCartName();
        if ($query->getOwnerId() !== null) {
            $criteria['owner_id'] = $query->getOwnerId();
        }

        return $this->gateway->countBy($criteria);
    }
}

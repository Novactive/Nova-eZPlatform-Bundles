<?php
declare(strict_types=1);

namespace AlmaviaCX\Ibexa\Commerce\Extra\Service\SiteAccessAware\Cart\Handler;

use AlmaviaCX\Ibexa\Commerce\Extra\Service\SiteAccessAware\Cart\NamedCartService;
use Ibexa\Cart\Persistence\Legacy\Cart\Handler\HandlerInterface;
use Ibexa\Cart\Persistence\Legacy\Cart\Handler\SessionHandler as BaseSessionHandler;
use Ibexa\Contracts\Cart\Value\CartQuery;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @phpstan-import-type T from \Ibexa\Cart\Persistence\Legacy\Cart\GatewayInterface as TCart
 */
final class SessionHandler extends NameCartHandler implements HandlerInterface
{
    public function __construct(
        private readonly SessionInterface $session,
        NamedCartService    $namedCartService,
        BaseSessionHandler $innerHandler,
    ) {
        parent::__construct($namedCartService, $innerHandler);
    }

    public function countMatching(CartQuery $query): int
    {
        return count($this->getCartsFromSession());
    }


    /**
     * @phpstan-return array<TCart>
     */
    private function getCartsFromSession(): array
    {

        $allCarts = array_filter(
            $this->session->all(),
            static fn (string $key): bool => str_starts_with($key, 'cart_item_'),
            ARRAY_FILTER_USE_KEY,
        );
        $carts = array_map(
            [$this->innerHandler, 'unserializeCart'],
            $allCarts
        );
        $name = $this->namedCartService->getNamedCartName();
        return array_filter(
            $carts,
            static fn (string $cart): bool => (($cart['name']?? '') === $name),
            ARRAY_FILTER_USE_KEY,
        );
    }
}

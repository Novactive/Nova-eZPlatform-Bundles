<?php

namespace AlmaviaCX\Ibexa\Commerce\Extra\Service\SiteAccessAware\Cart\Handler;

use AlmaviaCX\Ibexa\Commerce\Extra\Service\SiteAccessAware\Cart\NamedCartService;
use Ibexa\Cart\Persistence\Legacy\Cart\Gateway\StorageSchema;
use Ibexa\Cart\Persistence\Legacy\Cart\Handler\HandlerInterface;
use Ibexa\Cart\Persistence\Values\Cart;
use Ibexa\Cart\Persistence\Values\CartCreateStruct;
use Ibexa\Cart\Persistence\Values\CartMetadataUpdateStruct;
use Ibexa\Contracts\Cart\Value\CartQuery;

abstract class NameCartHandler implements HandlerInterface
{
    public function __construct(
        protected readonly NamedCartService $namedCartService,
        protected readonly HandlerInterface $innerHandler
    ) {
    }
    public function create(CartCreateStruct $createStruct): Cart
    {
        $createStruct->setName($this->namedCartService->getNamedCartName());
        return $this->innerHandler->create($createStruct);
    }

    public function update(CartMetadataUpdateStruct $updateStruct): void
    {
        $updateStruct->setName($this->namedCartService->getNamedCartName());
        $this->innerHandler->update($updateStruct);
    }

    public function delete(int $id): void
    {
        $this->innerHandler->delete($id);
    }

    public function findBy(
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        int $offset = 0
    ): array {
        $criteria['name'] = $this->namedCartService->getNamedCartName();
        return $this->innerHandler->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?Cart
    {
        $criteria['name'] = $this->namedCartService->getNamedCartName();
        return $this->innerHandler->findOneBy($criteria, $orderBy);
    }

    public function find(int $id): Cart
    {
        return $this->innerHandler->find($id);
    }

    public function exists(int $id): bool
    {
        return $this->innerHandler->exists($id);
    }

    public function findMatching(CartQuery $query): array
    {
        $offset = $query->getOffset();
        $limit = $query->getLimit();

        $criteria = [];
        if ($query->getOwnerId() !== null) {
            $criteria['owner_id'] = $query->getOwnerId();
        }

        $criteria['name'] = $this->namedCartService->getNamedCartName();

        return $this->findBy(
            $criteria,
            [
                StorageSchema::COLUMN_ID => 'ASC',
            ],
            $limit,
            $offset
        );
    }
}
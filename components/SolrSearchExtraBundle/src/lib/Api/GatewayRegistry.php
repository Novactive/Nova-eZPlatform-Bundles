<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Api;

use OutOfBoundsException;

class GatewayRegistry
{
    /** @var Gateway[] */
    private $gateways;

    /**
     * @param Gateway[] $gateways
     */
    public function __construct(array $gateways = [])
    {
        $this->gateways = $gateways;
    }

    /**
     * @return Gateway[]
     */
    public function getGateways(): array
    {
        return $this->gateways;
    }

    /**
     * @param Gateway[] $gateways
     */
    public function setGateways(array $gateways): void
    {
        $this->gateways = $gateways;
    }

    public function getGateway(string $connectionName): Gateway
    {
        if (!isset($this->gateways[$connectionName])) {
            throw new OutOfBoundsException(sprintf('No Gateway registered for connection \'%s\'', $connectionName));
        }

        return $this->gateways[$connectionName];
    }

    public function addGateway(string $connectionName, Gateway $gateway): void
    {
        $this->gateways[$connectionName] = $gateway;
    }

    public function hasGateway(string $connectionName): bool
    {
        return isset($this->gateways[$connectionName]);
    }
}

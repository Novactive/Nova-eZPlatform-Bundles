<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaOidc\Client\Provider;

use AlmaviaCX\Bundle\IbexaOidc\ResourceOwnerMapper\OidcResourceOwnerMapper;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class OidcGenericResourceOwner implements ResourceOwnerInterface
{
    public function __construct(
        protected array $response,
        protected string $resourceOwnerId = 'id',
    ) {
    }

    public function getId(): mixed
    {
        return $this->response[$this->resourceOwnerId];
    }

    public function getAttributeValue(string $name): mixed
    {
        return $this->response[$name] ?? null;
    }

    public function toArray(): array
    {
        return $this->response;
    }

    public function getUsername()
    {
        if (!empty($this->response['preferred_username'])) {
            return $this->response['preferred_username'];
        }

        return OidcResourceOwnerMapper::PROVIDER_PREFIX.$this->getId();
    }

    public function getEmail()
    {
        return $this->response['email'];
    }
}

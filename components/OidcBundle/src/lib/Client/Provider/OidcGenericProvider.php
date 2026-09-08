<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaOidc\Client\Provider;

use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessToken;

class OidcGenericProvider extends GenericProvider
{
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new OidcGenericResourceOwner(
            $response
        );
    }
}

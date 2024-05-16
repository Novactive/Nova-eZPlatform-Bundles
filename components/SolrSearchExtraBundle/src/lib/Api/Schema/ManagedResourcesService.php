<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Api\Schema;

use Novactive\EzSolrSearchExtra\Api\Gateway;

class ManagedResourcesService
{
    public const API_PATH = '/schema/managed';

    /**
     * ManagedResourcesService constructor.
     */
    public function __construct(protected Gateway $gateway)
    {
    }

    /**
     * @throws \Exception
     */
    public function getSets(): array
    {
        $response = $this->gateway->request(
            'GET',
            self::API_PATH
        );
        $sets = [];
        foreach ($response->managedResources as $infos) {
            $matches = [];
            if (preg_match('/^\/schema\/analysis\/([a-z]*)\/(.*)$/', $infos->resourceId, $matches)) {
                $sets[] = [
                    'type' => $matches[1],
                    'id' => $matches[2],
                ];
            }
        }

        return $sets;
    }
}

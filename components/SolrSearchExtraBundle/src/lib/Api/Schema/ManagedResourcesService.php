<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Api\Schema;

use Exception;
use Novactive\EzSolrSearchExtra\Search\ExtendedSearchHandler;

class ManagedResourcesService
{
    public const string API_PATH = '/schema/managed';

    public function __construct(protected ExtendedSearchHandler $searchHandler)
    {
    }

    /**
     * @throws Exception
     *
     * @return array<array{type: string, id: string}>
     */
    public function getSets(): array
    {
        $response = $this->searchHandler->request(
            'GET',
            self::API_PATH
        );
        $sets = [];
        foreach ($response->managedResources as $infos) {
            $matches = [];
            if (preg_match('/^\/schema\/analysis\/([a-z]*)\/(.*)$/', (string) $infos->resourceId, $matches)) {
                $sets[] = [
                    'type' => $matches[1],
                    'id' => $matches[2],
                ];
            }
        }

        return $sets;
    }
}

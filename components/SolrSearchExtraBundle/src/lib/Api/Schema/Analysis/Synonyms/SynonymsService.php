<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Api\Schema\Analysis\Synonyms;

use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Solr\Gateway\Message;
use Novactive\EzSolrSearchExtra\Api\Gateway;

class SynonymsService
{
    public const string API_PATH = '/schema/analysis/synonyms';
    
    /**
     * SynonymsService constructor.
     */
    public function __construct(protected Gateway $gateway)
    {
    }

    /**
     * @throws \Exception
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     *
     * @return SynonymsMap[]
     */
    public function getMappings(string $setId, int $offset = 0, int $limit = 10): array
    {
        $response = $this->gateway->request(
            'GET',
            sprintf('%s/%s', self::API_PATH, $setId)
        );

        if (null === $response) {
            throw new NotFoundException('synonym set', $setId);
        }

        $maps = [];
        foreach ($response->synonymMappings->managedMap as $term => $synonyms) {
            $maps[] = new SynonymsMap(
                $term,
                $synonyms
            );
        }

        return $maps;
    }

    /**
     * @throws \Exception
     */
    public function fetchTerm(string $setId, string $term): bool
    {
        $response = null;
        try {
            $response = $this->gateway->request(
                'GET',
                sprintf('%s/%s/%s', self::API_PATH, $setId, $term)
            );
        } catch (\Exception $exception) {
            return false;
        }
        if (null === $response) {
            return false;
        }
        if (404 === $response->responseHeader->status) {
            return false;
        }

        return true;
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     * @throws \Exception
     * @throws \Exception
     */
    public function addMapping(string $setId, SynonymsMap $map): bool
    {
        $termExist = $this->fetchTerm($setId, $map->getTerm());
        if ($termExist) {
            $this->deleteMapping($setId, $map->getTerm());
        }
        $response = $this->gateway->request(
            'PUT',
            sprintf('%s/%s', self::API_PATH, $setId),
            new Message(
                [
                    'Content-Type' => 'application/json',
                ],
                json_encode([$map->getTerm() => $map->getSynonyms()])
            )
        );

        if (404 === $response->responseHeader->status) {
            throw new NotFoundException('synonym set', $setId);
        }

        $this->gateway->reload();

        return 0 === $response->responseHeader->status;
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     * @throws \Exception
     * @throws \Exception
     */
    public function deleteMapping(string $setId, string $term): bool
    {
        $response = $this->gateway->request(
            'DELETE',
            sprintf('%s/%s/%s', self::API_PATH, $setId, urlencode($term))
        );

        if (404 === $response->responseHeader->status) {
            throw new NotFoundException('synonym', $term);
        }

        $this->gateway->reload();

        return 0 === $response->responseHeader->status;
    }
}

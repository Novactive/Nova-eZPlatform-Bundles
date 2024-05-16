<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Api\Schema\Analysis\Stopwords;

use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Solr\Gateway\Message;
use Novactive\EzSolrSearchExtra\Api\Gateway;

class StopwordsService
{
    public const string API_PATH = '/schema/analysis/stopwords';
    
    /**
     * StopwordsService constructor.
     */
    public function __construct(protected Gateway $gateway)
    {
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     * @throws \Exception
     */
    public function getWords(string $setId, int $offset = 0, int $limit = 10): array
    {
        $response = $this->gateway->request(
            'GET',
            sprintf('%s/%s', self::API_PATH, $setId)
        );

        if (null === $response) {
            throw new NotFoundException('stopword set', $setId);
        }

        return $response->wordSet->managedList;
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     * @throws \Exception
     */
    public function addWords(string $setId, array $words): bool
    {
        $response = $this->gateway->request(
            'PUT',
            sprintf('%s/%s', self::API_PATH, $setId),
            new Message(
                [
                    'Content-Type' => 'application/json',
                ],
                json_encode($words)
            )
        );

        if (404 === $response->responseHeader->status) {
            throw new NotFoundException('stopword set', $setId);
        }

        $this->gateway->reload();

        return 0 === $response->responseHeader->status;
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     * @throws \Exception
     */
    public function deleteWord(string $setId, string $word): bool
    {
        $response = $this->gateway->request(
            'DELETE',
            sprintf('%s/%s/%s', self::API_PATH, $setId, $word)
        );

        if (404 === $response->responseHeader->status) {
            throw new NotFoundException('stopword', $word);
        }

        $this->gateway->reload();

        return 0 === $response->responseHeader->status;
    }
}

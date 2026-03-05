<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Api\Schema\Analysis\Stopwords;

use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Solr\Gateway\Message;
use Novactive\EzSolrSearchExtra\Search\ExtendedSearchHandler;

class StopwordsService
{
    public const API_PATH = '/schema/analysis/stopwords';

    protected ExtendedSearchHandler $searchHandler;

    public function __construct(ExtendedSearchHandler $searchHandler)
    {
        $this->searchHandler = $searchHandler;
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     * @throws \Exception
     */
    public function getWords(string $setId, int $offset = 0, int $limit = 10): array
    {
        $response = $this->searchHandler->request(
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
        $response = $this->searchHandler->request(
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

        $this->searchHandler->reload();

        return 0 === $response->responseHeader->status;
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     * @throws \Exception
     */
    public function deleteWord(string $setId, string $word): bool
    {
        $response = $this->searchHandler->request(
            'DELETE',
            sprintf('%s/%s/%s', self::API_PATH, $setId, $word)
        );

        if (404 === $response->responseHeader->status) {
            throw new NotFoundException('stopword', $word);
        }

        $this->searchHandler->reload();

        return 0 === $response->responseHeader->status;
    }
}

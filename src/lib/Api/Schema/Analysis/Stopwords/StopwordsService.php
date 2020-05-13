<?php

/**
 * NovaeZSolrSearchExtraBundle.
 *
 * @package   NovaeZSolrSearchExtraBundle
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZSolrSearchExtraBundle/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Api\Schema\Analysis\Stopwords;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use EzSystems\EzPlatformSolrSearchEngine\Gateway\Message;
use Novactive\EzSolrSearchExtra\Api\Gateway;

class StopwordsService
{
    public const API_PATH = '/schema/analysis/stopwords';

    /** @var Gateway */
    protected $gateway;

    /**
     * StopwordsService constructor.
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

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

<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\TextExtractor;

use Novactive\EzSolrSearchExtra\Tika\TikaClientInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Class TikaTextExtractor.
 *
 * @package Novactive\EzSolrSearchExtra\TextExtractor
 */
class TikaTextExtractor implements TextExtractorInterface
{
    /** @var TikaClientInterface */
    private $tikaClient;

    /** @var LoggerInterface */
    private $logger;

    /**
     * TikaTextExtractor constructor.
     */
    public function __construct(TikaClientInterface $tikaClient, LoggerInterface $logger)
    {
        $this->tikaClient = $tikaClient;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function extract($fileName): ?string
    {
        try {
            $plaintext = $this->tikaClient->getText($fileName);

            return preg_replace('([\x09]+)', ' ', (string) $plaintext);
        } catch (RuntimeException $e) {
            $errorMsg = $e->getMessage();
            $this->logger->error("Error when converting file $fileName\n$errorMsg");
        }

        return null;
    }
}

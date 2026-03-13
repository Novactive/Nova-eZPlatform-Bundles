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
    /**
     * TikaTextExtractor constructor.
     */
    public function __construct(
        private readonly TikaClientInterface $tikaClient,
        private readonly LoggerInterface $logger
    ) {
    }

    public function extract(string $fileName): ?string
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

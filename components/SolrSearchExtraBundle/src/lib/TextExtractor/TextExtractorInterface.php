<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\TextExtractor;

/**
 * Interface TextExtractorInterface.
 *
 * @package Novactive\EzSolrSearchExtra\TextExtractor
 */
interface TextExtractorInterface
{
    /**
     * Extract text from a file.
     *
     * @param $fileName
     */
    public function extract($fileName): ?string;
}

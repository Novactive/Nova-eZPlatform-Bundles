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

<?php
/**
 * NovaeZSolrSearchExtraBundle.
 *
 * @package   NovaeZSolrSearchExtraBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
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
     *
     * @return string|null
     */
    public function extract($fileName): ?string;
}

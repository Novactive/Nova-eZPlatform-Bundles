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

namespace Novactive\EzSolrSearchExtra\Tika;

/**
 * Interface TikaClientInterface.
 *
 * @package Novactive\EzSolrSearchExtra\Tika
 */
interface TikaClientInterface
{
    /**
     * @param $fileName
     */
    public function getText($fileName): ?string;
}

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

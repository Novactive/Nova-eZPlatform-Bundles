<?php

declare(strict_types=1);

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

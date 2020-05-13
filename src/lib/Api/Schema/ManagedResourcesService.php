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

namespace Novactive\EzSolrSearchExtra\Api\Schema;

use Novactive\EzSolrSearchExtra\Api\Gateway;

class ManagedResourcesService
{
    public const API_PATH = '/schema/managed';

    /** @var Gateway */
    protected $gateway;

    /**
     * ManagedResourcesService constructor.
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function getSets()
    {
        $response = $this->gateway->request(
            'GET',
            self::API_PATH
        );
        $sets = [];
        foreach ($response->managedResources as $infos) {
            $matches = [];
            if (preg_match('/^\/schema\/analysis\/([a-z]*)\/(.*)$/', $infos->resourceId, $matches)) {
                $sets[]                = [
                    'type' => $matches[1],
                    'id'   => $matches[2],
                ];
            }
        }

        return $sets;
    }
}

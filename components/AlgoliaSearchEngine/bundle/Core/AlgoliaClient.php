<?php

/**
 * Nova eZ Algolia Search Engine.
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @licence   "SEE FULL LICENSE OPTIONS IN LICENSE.md"
 *            Nova eZ Algolia Search Engine is tri-licensed, meaning you must choose one of three licenses to use:
 *                - Commercial License: a paid license, meant for commercial use. The default option for most users.
 *                - Creative Commons Non-Commercial No-Derivatives: meant for trial and non-commercial use.
 *                - GPLv3 License: meant for open-source projects.
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Core;

use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\Repository\Permission\PermissionCriterionResolver;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\CriterionVisitor\CriterionVisitor;
use Novactive\Bundle\eZAlgoliaSearchEngine\DependencyInjection\Configuration;
use RuntimeException;

final class AlgoliaClient
{
    public const CLIENT_ADMIN_MODE = 'admin';
    public const CLIENT_SEARCH_MODE = 'search';

    public const CLIENT_MODES = [
        self::CLIENT_ADMIN_MODE,
        self::CLIENT_SEARCH_MODE,
    ];

    /**
     * @var array
     */
    private $indexes;

    /**
     * @var array
     */
    private $config;

    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var PermissionCriterionResolver
     */
    private $permissionCriterionResolver;

    /**
     * @var CriterionVisitor
     */
    private $dispatcherCriterionVisitor;

    public function __construct(
        ConfigResolverInterface $configResolver,
        PermissionCriterionResolver $permissionCriterionResolver,
        CriterionVisitor $dispatcherCriterionVisitor
    ) {
        $this->configResolver = $configResolver;
        $this->permissionCriterionResolver = $permissionCriterionResolver;
        $this->dispatcherCriterionVisitor = $dispatcherCriterionVisitor;
        $this->config = [
            'index_name_prefix' => $this->configResolver->getParameter(
                'index_name_prefix',
                Configuration::NAMESPACE
            ),
            'app_id' => $this->configResolver->getParameter('app_id', Configuration::NAMESPACE),
            'api_secret_key' => $this->configResolver->getParameter('api_secret_key', Configuration::NAMESPACE),
            'api_search_only_key' => $this->configResolver->getParameter(
                'api_search_only_key',
                Configuration::NAMESPACE
            ),
        ];
    }

    private function getIndex(string $languageCode, string $mode, ?string $replicaSuffix = null): SearchIndex
    {
        $indexName = $this->config['index_name_prefix'].'-'.$languageCode;

        if (null !== $replicaSuffix) {
            $indexName .= '-'.$replicaSuffix;
        }

        if (isset($this->indexes[$indexName])) {
            return $this->indexes[$indexName];
        }

        if (!\in_array($mode, self::CLIENT_MODES, true)) {
            throw new InvalidArgumentException('$mode', 'The Index mode must either "admin" or "search".');
        }
        $apiKey = (self::CLIENT_ADMIN_MODE === $mode) ? $this->config['api_secret_key'] : $this->getSecuredApiKey();
        $client = SearchClient::create($this->config['app_id'], $apiKey);
        $this->indexes[$indexName] = $client->initIndex($indexName);

        $dir = __DIR__;
        $licenseKey = file_get_contents("{$dir}/../Resources/license.key");
        [$header, $payload, $signature] = explode(
            '.',
            $this->configResolver->getParameter(
                'license_key',
                Configuration::NAMESPACE
            )
        );
        if (
            ('0c570e16c2396c4a518f086cc61b726a' !== md5_file("{$dir}/../Resources/license.key")) ||
            1 !== openssl_verify(
                "{$header}.{$payload}",
                base64_decode(strtr($signature, '-_', '+/')),
                openssl_pkey_get_public($licenseKey),
                OPENSSL_ALGO_SHA256
            )
        ) {
            throw new RuntimeException('License Key is invalid! Please contact Novactive');
        }

        if (json_decode(base64_decode(strtr($payload, '-_', '+/')))->exp < time()) {
            throw new RuntimeException('License Key has expired! Please contact Novactive');
        }

        return $this->indexes[$indexName];
    }

    public function __invoke(
        callable $callback,
        string $languageCode,
        string $mode = self::CLIENT_SEARCH_MODE,
        ?string $replicaSuffix = null
    ) {
        $index = $this->getIndex($languageCode, $mode, $replicaSuffix);

        return $callback($index);
    }

    public function getSecuredApiKey(): string
    {
        $apiSearchOnlyKey = $this->configResolver->getParameter('api_search_only_key', Configuration::NAMESPACE);

        $permissionsCriterion = $this->permissionCriterionResolver->getPermissionsCriterion('content', 'read');
        if (!$permissionsCriterion instanceof Criterion) {
            return $apiSearchOnlyKey;
        }

        return SearchClient::generateSecuredApiKey(
            $apiSearchOnlyKey,
            [
                'filters' => $this->dispatcherCriterionVisitor->visit(
                    $this->dispatcherCriterionVisitor,
                    $permissionsCriterion
                ),
            ]
        );
    }
}

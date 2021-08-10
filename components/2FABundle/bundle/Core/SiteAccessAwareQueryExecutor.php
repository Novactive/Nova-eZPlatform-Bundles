<?php

/*
 * NovaeZ2FABundle.
 *
 * @package   NovaeZ2FABundle
 *
 * @author    Yassine HANINI
 * @copyright 2021 AlmaviaCX
 * @license   https://github.com/Novactive/NovaeZ2FA/blob/main/LICENSE
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZ2FABundle\Core;

use Doctrine\Persistence\ManagerRegistry as Registry;
use eZ\Bundle\EzPublishCoreBundle\ApiLoader\RepositoryConfigurationProvider;
use Doctrine\DBAL\Connection;

final class SiteAccessAwareQueryExecutor
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var RepositoryConfigurationProvider
     */
    private $repositoryConfigurationProvider;

    public function __construct(Registry                        $registry,
                                RepositoryConfigurationProvider $repositoryConfigurationProvider
    ) {
        $this->registry = $registry;
        $this->repositoryConfigurationProvider = $repositoryConfigurationProvider;
    }

    private function getConnectionName(): string
    {
        $config = $this->repositoryConfigurationProvider->getRepositoryConfig();

        return $config['storage']['connection'] ?? 'default';
    }

    public function __invoke(string $query, array $params, array $types)
    {
        $cleanQuery = trim($query);
        /** @var Connection $connection */
        $connection = $this->registry->getConnection($this->getConnectionName());

        if (stripos($cleanQuery, 'select') === 0) {
            return $connection->executeQuery($cleanQuery, $params, $types);
        }

        return $connection->executeStatement($cleanQuery, $params, $types);
    }

}

<?php

/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Core\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Novactive\Bundle\eZMailingBundle\Entity\User as UserEntity;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class User
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getPagerFilters(array $filters = [], int $page = 1, int $limit = 25): Pagerfanta
    {
        $repo = $this->entityManager->getRepository(UserEntity::class);
        $adapter = new DoctrineORMAdapter($repo->createQueryBuilderForFilters($filters));
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $pager;
    }

    public function getStatusesData(array $filters = []): array
    {
        unset($filters['status']);
        $repo = $this->entityManager->getRepository(UserEntity::class);
        $total = 0;
        $statuses = [];
        foreach (UserEntity::STATUSES as $status) {
            $statuses[$status] = $repo->countByFilters($filters + ['status' => $status]);

            $total += $statuses[$status];
        }

        return ['count' => $total, 'results' => $statuses];
    }
}

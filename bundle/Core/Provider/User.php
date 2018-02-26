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

use Doctrine\ORM\EntityManager;
use Novactive\Bundle\eZMailingBundle\Entity\User as UserEntity;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class User
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * User constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param array $filters
     * @param int   $page
     * @param int   $limit
     *
     * @return Pagerfanta
     */
    public function getPagerFilters(array $filters = [], int $page = 1, int $limit = 25): Pagerfanta
    {
        $repo    = $this->entityManager->getRepository(UserEntity::class);
        $adapter = new DoctrineORMAdapter($repo->createQueryBuilderForFilters($filters));
        $pager   = new Pagerfanta($adapter);
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $pager;
    }

    /**
     * @param array $filters
     *
     * @return array
     */
    public function getStatusesData(array $filters = []): array
    {
        unset($filters['status']);
        $repo  = $this->entityManager->getRepository(UserEntity::class);
        $total = 0;
        foreach (UserEntity::STATUSES as $statusId => $statusKey) {
            $statuses[$statusId] = $repo->countByFilters($filters + ['status' => $statusId]);

            $total += $statuses[$statusId];
        }

        return ['count' => $total, 'results' => $statuses];
    }
}

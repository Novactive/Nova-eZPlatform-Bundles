<?php

/**
 * NovaeZProtectedContentBundle.
 *
 * @package   Novactive\Bundle\eZProtectedContentBundle
 *
 * @author    Novactive
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/eZProtectedContentBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZProtectedContentBundle\Core\Compose;

use Doctrine\ORM\EntityManagerInterface;

trait EntityManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @required
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    protected function persitFlush(object $item): void
    {
        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }
}

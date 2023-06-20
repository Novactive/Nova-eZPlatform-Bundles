<?php

namespace Novactive\EzRssFeedBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;

trait EntityManagerTrait
{
    public EntityManagerInterface $entityManager;

    /**
     * @required
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }
}

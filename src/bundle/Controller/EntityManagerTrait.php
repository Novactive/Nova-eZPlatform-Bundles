<?php

namespace Novactive\EzRssFeedBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;

trait EntityManagerTrait
{
    /** @var EntityManagerInterface */
    public $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @required
     */
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}
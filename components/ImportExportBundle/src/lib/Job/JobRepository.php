<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class JobRepository extends EntityRepository implements ServiceEntityRepositoryInterface
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(Job::class));
    }

    public function findById(int $id): ?Job
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function save(Job $job): void
    {
        $this->_em->persist($job);
        $this->_em->flush();
    }

    public function delete(Job $job): void
    {
        $this->_em->remove($job);
        $this->_em->flush();
    }
}

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

    /**
     * @return array<int, int>
     */
    public function getJobLogsCountByLevel(int $jobId): array
    {
        $qb = $this->_em->getConnection()->createQueryBuilder();
        $qb->select('count(id) as count, level');
        $qb->from('import_export_job_record');
        $qb->where($qb->expr()->eq('job_id', ':jobId'));
        $qb->groupBy('level');
        $qb->setParameter('jobId', $jobId);

        $rows = $qb->execute()->fetchAllAssociative();

        return array_combine(
            array_column($rows, 'level'),
            array_column($rows, 'count'),
        );
    }
}

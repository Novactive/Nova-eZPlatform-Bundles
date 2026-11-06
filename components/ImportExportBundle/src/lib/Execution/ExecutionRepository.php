<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Execution;

use AlmaviaCX\Bundle\IbexaImportExport\Job\Job;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends EntityRepository<Execution>
 */
class ExecutionRepository extends EntityRepository implements ServiceEntityRepositoryInterface
{
    public function __construct(
        EntityManagerInterface $em
    ) {
        parent::__construct($em, $em->getClassMetadata(Execution::class));
    }

    public function refresh(Execution $execution): void
    {
        $this->_em->refresh($execution);
    }

    public function findById(int $id): ?Execution
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function save(Execution $execution): void
    {
        $this->_em->persist($execution);
        $this->_em->flush();
    }

    public function delete(Execution $execution): void
    {
        $this->_em->remove($execution);
        $this->_em->flush();
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     *
     * @return array<int, int>
     */
    public function getExecutionLogsCountByLevel(int $jobId): array
    {
        $qb = $this->_em->getConnection()->createQueryBuilder();
        $qb->select('count(id) as count, level');
        $qb->from('import_export_execution_record');
        $qb->where($qb->expr()->eq('execution_id', ':executionId'));
        $qb->groupBy('level');
        $qb->setParameter('executionId', $jobId);

        $rows = $qb->execute()->fetchAllAssociative();

        return array_combine(
            array_column($rows, 'level'),
            array_column($rows, 'count'),
        );
    }

    public function getJobExecutionQueryBuilder(Job $job): QueryBuilder
    {
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.job = :job');
        $qb->setParameter('job', $job);
        $qb->orderBy('e.id', 'DESC');

        return $qb;
    }
}

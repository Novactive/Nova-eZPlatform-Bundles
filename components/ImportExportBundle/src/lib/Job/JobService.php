<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job;

use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

class JobService
{
    protected JobRepository $jobRepository;
    protected JobRunnerInterface $jobRunner;
    protected ConfigResolverInterface $configResolver;
    protected JobDebugger $jobDebugger;

    /**
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Job\JobRepository      $jobRepository
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Job\JobRunnerInterface $jobRunner
     */
    public function __construct(
        JobRepository $jobRepository,
        JobRunnerInterface $jobRunner,
        JobDebugger $jobDebugger,
        ConfigResolverInterface $configResolver
    ) {
        $this->jobDebugger = $jobDebugger;
        $this->configResolver = $configResolver;
        $this->jobRepository = $jobRepository;
        $this->jobRunner = $jobRunner;
    }

    public function createJob(Job $job, bool $autoStart = true)
    {
        $job->setRequestedDate(new DateTimeImmutable());
        $job->setStatus(Job::STATUS_PENDING);

        $this->jobRepository->save($job);

        if ($autoStart) {
            $this->runJob($job);
        }
    }

    public function runJob(Job $job, int $batchLimit = null, bool $reset = false): void
    {
        if (!$batchLimit) {
            $batchLimit = $this->configResolver->getParameter('default_batch_limit', 'import_export');
        }
        ($this->jobRunner)($job, $batchLimit, $reset);
    }

    public function cancelJob(Job $job)
    {
        $job->setStatus(Job::STATUS_CANCELED);
        $this->jobRepository->save($job);
    }

    public function debug(Job $job, int $index)
    {
        ($this->jobDebugger)($job, $index);
    }

    public function loadJobById(int $id): ?Job
    {
        return $this->jobRepository->findById($id);
    }

    public function countJobs(): int
    {
        return $this->jobRepository->count([]);
    }

    public function loadJobs($limit = 10, $offset = 0): array
    {
        return $this->jobRepository->findBy(
            [],
            ['requestedDate' => 'DESC', 'id' => 'DESC'],
            $limit,
            $offset
        );
    }

    public function delete(Job $job): void
    {
        $this->jobRepository->delete($job);
    }

    public function getJobLogs(Job $job, ?int $level = null): Collection
    {
        if (!$level) {
            return $job->getRecords();
        }

        return $job->getRecordsForLevel($level);
    }

    /**
     * @return array<int, int>
     */
    public function getJobLogsCountByLevel(Job $job): array
    {
        return $this->jobRepository->getJobLogsCountByLevel($job->getId());
    }
}

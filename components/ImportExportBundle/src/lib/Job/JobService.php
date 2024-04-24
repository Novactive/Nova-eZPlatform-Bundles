<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job;

use DateTimeImmutable;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

class JobService
{
    protected JobRepository $jobRepository;
    protected JobRunnerInterface $jobRunner;
    protected ConfigResolverInterface $configResolver;

    /**
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Job\JobRepository      $jobRepository
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Job\JobRunnerInterface $jobRunner
     */
    public function __construct(JobRepository $jobRepository, JobRunnerInterface $jobRunner)
    {
        $this->jobRepository = $jobRepository;
        $this->jobRunner = $jobRunner;
    }

    public function createJob(Job $job)
    {
        $job->setRequestedDate(new DateTimeImmutable());
        $job->setStatus(Job::STATUS_PENDING);

        $this->jobRepository->save($job);

        $this->runJob($job);
    }

    public function runJob(Job $job, bool $force = false): void
    {
        ($this->jobRunner)($job, $force);
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
}

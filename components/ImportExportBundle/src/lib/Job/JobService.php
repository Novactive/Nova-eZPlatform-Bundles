<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job;

use AlmaviaCX\Bundle\IbexaImportExport\Execution\Execution;
use AlmaviaCX\Bundle\IbexaImportExport\Execution\ExecutionOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Execution\ExecutionRecord;
use AlmaviaCX\Bundle\IbexaImportExport\Execution\ExecutionRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use InvalidArgumentException;

/**
 * @SuppressWarnings("PHPMD.TooManyPublicMethods")
 */
class JobService
{
    public function __construct(
        protected JobRepository $jobRepository,
        protected ExecutionRepository $executionRepository,
        protected JobRunnerInterface $jobRunner,
        protected JobDebugger $jobDebugger,
        protected ConfigResolverInterface $configResolver,
        protected PermissionResolver $permissionResolver
    ) {
    }

    public function createJob(Job $job, bool $autoStart = false): void
    {
        if (!$this->permissionResolver->hasAccess('import_export', 'job.create')) {
            throw new UnauthorizedException('import_export', 'job.create');
        }
        $job->setRequestedDate(new DateTimeImmutable());
        $this->jobRepository->save($job);

        if ($autoStart) {
            $this->runJob($job);
        }
    }

    public function runJob(Job $job, ?int $batchLimit = null, bool $reset = false): int
    {
        if (!$this->permissionResolver->hasAccess('import_export', 'job.execute')) {
            throw new UnauthorizedException('import_export', 'job.execute');
        }

        if (!$batchLimit) {
            $batchLimit = $this->configResolver->getParameter('default_batch_limit', 'import_export');
        }

        return ($this->jobRunner)($job, $batchLimit, $reset);
    }

    public function executeJob(
        Job $job,
        ExecutionOptions $options = new ExecutionOptions(),
        ?int $batchLimit = null,
        ?int $executionId = null,
        bool $async = true
    ): Execution {
        if (!$this->permissionResolver->hasAccess('import_export', 'job.execute')) {
            throw new UnauthorizedException('import_export', 'job.execute');
        }
        if (!$batchLimit) {
            $batchLimit = $this->configResolver->getParameter('default_batch_limit', 'import_export');
        }

        if ($executionId) {
            $execution = $this->executionRepository->findById($executionId);
            if (!$execution->canRun()) {
                throw new InvalidArgumentException('Execution already running');
            }
        } else {
            $execution = new Execution($options);
            $job->addExecution($execution);
            $this->jobRepository->save($job);
        }

        if ($this->jobRunner instanceof AsyncJobRunner) {
            $this->jobRunner->runExecution($execution, $batchLimit, $async);
        } else {
            $this->jobRunner->runExecution($execution, $batchLimit);
        }

        return $execution;
    }

    public function retryExecution(Execution $execution, ?int $batchLimit = null): Execution
    {
        $newExecution = new Execution($execution->getOptions());
        $job = $execution->getJob();
        $job->addExecution($newExecution);
        $this->jobRepository->save($job);

        $this->runExecution($newExecution, $batchLimit);

        return $newExecution;
    }

    public function runExecution(Execution $execution, ?int $batchLimit = null): void
    {
        if (!$this->permissionResolver->hasAccess('import_export', 'job.execute')) {
            throw new UnauthorizedException('import_export', 'job.execute');
        }
        if (!$batchLimit) {
            $batchLimit = $this->configResolver->getParameter('default_batch_limit', 'import_export');
        }

        $this->jobRunner->runExecution($execution, $batchLimit);
    }

    public function pauseJobExecution(Execution $execution): void
    {
        if (!$this->permissionResolver->hasAccess('import_export', 'job.execute')) {
            throw new UnauthorizedException('import_export', 'job.execute');
        }
        $execution->setStatus(Execution::STATUS_FORCE_PAUSED);
        $this->executionRepository->save($execution);
    }

    public function cancelJobExecution(Execution $execution): void
    {
        if (!$this->permissionResolver->hasAccess('import_export', 'job.execute')) {
            throw new UnauthorizedException('import_export', 'job.execute');
        }
        $execution->setStatus(Execution::STATUS_CANCELED);
        $this->executionRepository->save($execution);
    }

    public function debugJobExecution(Execution $execution, int $index): void
    {
        if (!$this->permissionResolver->hasAccess('import_export', 'job.execute')) {
            throw new UnauthorizedException('import_export', 'job.execute');
        }
        ($this->jobDebugger)($execution, $index);
    }

    public function loadJobById(int $id): ?Job
    {
        if (!$this->permissionResolver->hasAccess('import_export', 'job.view')) {
            throw new UnauthorizedException('import_export', 'job.view');
        }

        return $this->jobRepository->findById($id);
    }

    public function loadJobByUlid(string $ulid): ?Job
    {
        if (!$this->permissionResolver->hasAccess('import_export', 'job.view')) {
            throw new UnauthorizedException('import_export', 'job.view');
        }

        return $this->jobRepository->findByUlid($ulid);
    }

    public function countJobs(): int
    {
        return $this->jobRepository->count([]);
    }

    /**
     * @param $limit
     * @param $offset
     *
     * @return Job[]
     */
    public function loadJobs(int $limit = 10, int $offset = 0): array
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
        if (!$this->permissionResolver->hasAccess('import_export', 'job.delete')) {
            throw new UnauthorizedException('import_export', 'job.delete');
        }
        $this->jobRepository->delete($job);
    }

    /**
     * @return Collection<string, ExecutionRecord>|Selectable<string, ExecutionRecord>
     */
    public function getJobExecutionLogs(Execution $execution, ?int $level = null): Collection|Selectable
    {
        if (!$level) {
            return $execution->getLoggerRecords();
        }

        return $execution->getRecordsForLevel($level);
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     *
     * @return array<int, int>
     */
    public function getJobExecutionLogsCountByLevel(Execution $execution): array
    {
        return $this->executionRepository->getExecutionLogsCountByLevel($execution->getId());
    }
}

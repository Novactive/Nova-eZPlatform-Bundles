<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExportBundle\Command;

use AlmaviaCX\Bundle\IbexaImportExport\Execution\Execution;
use AlmaviaCX\Bundle\IbexaImportExport\Execution\ExecutionRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PurgeExecutionsCommand extends Command
{
    private const DEFAULT_ITERATION_COUNT = 500;
    protected static $defaultName = 'import_export:execution:purge';

    public function __construct(
        protected ExecutionRepository $executionRepository,
        protected EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        parent::configure();
        $this->addOption(
            'job_id',
            'j',
            InputOption::VALUE_OPTIONAL
        );
        $this->addOption(
            'until',
            'u',
            InputOption::VALUE_OPTIONAL,
            'ISO 8601 formated date (ex: 2004-02-12T15:19:21+00:00)'
        );
        $this->addOption(
            'status',
            's',
            InputOption::VALUE_OPTIONAL,
            default: Execution::STATUS_COMPLETED
        );

        $this->addOption(
            'batch-size',
            'c',
            InputOption::VALUE_REQUIRED,
            'Number of executions to delete in a single iteration.',
            self::DEFAULT_ITERATION_COUNT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobId = $input->getOption('job_id');
        $qb = $this->executionRepository->createQueryBuilder('e');
        $qb->setMaxResults($input->getOption('batch-size'));

        if (null !== $jobId) {
            $qb->andWhere('e.job = :job');
            $qb->setParameter('job', $jobId, Types::INTEGER);
        }

        $qb->innerJoin('e.workflowState', 's');
        $qb->where($qb->expr()->orX(
            $qb->expr()->lte('s.endTime', ':until'),
            $qb->expr()->andX(
                $qb->expr()->lte('s.startTime', ':until'),
                $qb->expr()->isNull('s.endTime'),
            )
        ));

        $untilInput = $input->getOption('until');
        if ($untilInput) {
            $until = DateTime::createFromFormat('c', $untilInput);
        } else {
            $until = new DateTime();
            $until->modify('-2 week');
        }
        $qb->setParameter('until', $until, Types::DATETIME_MUTABLE);
        $qb->orderBy('e.id', 'ASC');

        $output->writeln('Purging executions...');
        $progressBar = new ProgressBar($output);
        $progressBar->start();
        do {
            /** @var Execution[] $executions */
            $executions = $qb->getQuery()->getResult();
            $executionsCount = count($executions);
            foreach ($executions as $execution) {
                $records = $execution->getLoggerRecords();
                do {
                    $recordsSlice = $records->slice(0, 500);
                    $recordsSliceCount = count($recordsSlice);
                    foreach ($recordsSlice as $record) {
                        $this->em->remove($record);
                    }
                    $this->em->flush();
                } while (500 === $recordsSliceCount);

                $records = null;
                $this->em->remove($execution);
                $this->em->flush();
                $progressBar->advance();
            }
            $this->em->clear();
        } while ($executionsCount > 0);

        return Execution::STATUS_COMPLETED;
    }
}

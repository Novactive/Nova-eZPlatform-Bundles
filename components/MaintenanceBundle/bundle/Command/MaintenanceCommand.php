<?php

/**
 * NovaeZMaintenanceBundle.
 *
 * @package   Novactive\NovaeZMaintenanceBundle
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZMaintenanceBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\NovaeZMaintenanceBundle\Command;

use Novactive\NovaeZMaintenanceBundle\Helper\FileHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class MaintenanceCommand extends Command
{
    /**
     * @var FileHelper
     */
    protected $fileHelper;

    public function __construct(FileHelper $fileHelper)
    {
        parent::__construct();
        $this->fileHelper = $fileHelper;
    }

    protected function configure(): void
    {
        parent::configure();
        $this->setName('novamaintenance:set');
        $this->addOption('lock', null, InputOption::VALUE_NONE, 'Enable Maintenance');
        $this->addOption('unlock', null, InputOption::VALUE_NONE, 'Disable Maintenance');
    }

    private function unlock(): string
    {
        return $this->fileHelper->maintenanceUnLock() ? 'Maintenance unlocked' : 'Maintenance already unlocked';
    }

    private function lock(): string
    {
        return $this->fileHelper->maintenanceLock() ? 'Maintenance locked' : 'Maintenance already locked';
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (true === $input->getOption('lock')) {
            $output->writeln('<info>'.$this->lock().'</info>');
        } elseif (true === $input->getOption('unlock')) {
            $output->writeln('<info>'.$this->unlock().'</info>');
        }

        return Command::SUCCESS;
    }
}

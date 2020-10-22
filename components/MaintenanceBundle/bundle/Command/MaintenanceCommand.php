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

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Novactive\NovaeZMaintenanceBundle\Helper\FileHelper;
use RuntimeException;
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

    /**
     * @var ConfigResolverInterface
     */
    protected $configResolver;

    public function __construct(ConfigResolverInterface $configResolver, FileHelper $fileHelper)
    {
        parent::__construct();
        $this->configResolver = $configResolver;
        $this->fileHelper = $fileHelper;
    }

    protected function configure(): void
    {
        parent::configure();
        $this->setName('novamaintenance:set');
        $this->addOption('lock', null, InputOption::VALUE_NONE, 'Enable Maintenance');
        $this->addOption('unlock', null, InputOption::VALUE_NONE, 'Disable Maintenance');
    }

    public function unlock(): string
    {
        $this->assertMaintenanceEnabled();
        $filePath = $this->configResolver->getParameter('lock_file_id', 'nova_ezmaintenance');

        return $this->fileHelper->maintenanceUnLock($filePath) ? 'Maintenance unlock' : 'Maintenance already enabled';
    }

    public function lock(): string
    {
        $this->assertMaintenanceEnabled();
        $filePath = $this->configResolver->getParameter('lock_file_id', 'nova_ezmaintenance');

        return $this->fileHelper->maintenanceLock($filePath) ? 'Maintenance lock' : 'Maintenance already disabled';
    }

    protected function assertMaintenanceEnabled(): void
    {
        if (true !== $this->configResolver->getParameter('enable', 'nova_ezmaintenance')) {
            throw new RuntimeException('Maintenance not activeted for this siteaccess');
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (true === $input->getOption('lock')) {
            $output->writeln('<info>'.$this->lock().'</info>');
        }
        if (true === $input->getOption('unlock')) {
            $output->writeln('<info>'.$this->unlock().'</info>');
        }

        return Command::SUCCESS;
    }
}

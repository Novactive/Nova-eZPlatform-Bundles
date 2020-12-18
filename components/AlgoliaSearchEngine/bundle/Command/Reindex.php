<?php

/**
 * Nova eZ Algolia Search Engine.
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @licence   "SEE FULL LICENSE OPTIONS IN LICENSE.md"
 *            Nova eZ Algolia Search Engine is tri-licensed, meaning you must choose one of three licenses to use:
 *                - Commercial License: a paid license, meant for commercial use. The default option for most users.
 *                - Creative Commons Non-Commercial No-Derivatives: meant for trial and non-commercial use.
 *                - GPLv3 License: meant for open-source projects.
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Command;

use eZ\Publish\SPI\Persistence\Handler as PersistenceHandler;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\Handler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Just a command to help debugging when contributing.
 */
final class Reindex extends Command
{
    protected static $defaultName = 'nova:ez:algolia:reindex';

    /**
     * @var Handler
     */
    private $handler;

    /**
     * @var PersistenceHandler
     */
    private $persistenceHandler;

    protected function configure(): void
    {
        $this
            ->setHidden(true)
            ->setName(self::$defaultName)
            ->setDescription('Reindex the specific Location by provided Id and Content assigned to it.')
            ->addArgument('locationId', InputArgument::REQUIRED, 'Location Id');
    }

    /**
     * @required
     */
    public function setDependencies(Handler $handler, PersistenceHandler $persistenceHandler): void
    {
        $this->handler = $handler;
        $this->persistenceHandler = $persistenceHandler;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $location = $this->persistenceHandler->locationHandler()->load($input->getArgument('locationId'));
        $this->handler->indexLocation($location);
        $this->handler->indexContent($this->persistenceHandler->contentHandler()->load($location->contentId));

        $io->success('Done.');

        return 0;
    }
}

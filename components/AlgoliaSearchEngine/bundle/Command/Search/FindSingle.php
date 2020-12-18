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

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Command\Search;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Just a command to help debugging when contributing.
 */
final class FindSingle extends Command
{
    protected static $defaultName = 'nova:ez:algolia:find:single';

    /**
     * @var Repository
     */
    private $repository;

    protected function configure(): void
    {
        $this
            ->setHidden(true)
            ->setName(self::$defaultName)
            ->setDescription('Fetching the single Content by Id.')
            ->addArgument('contentId', InputArgument::REQUIRED, 'Content Id');
    }

    /**
     * @required
     */
    public function setDependencies(Repository $repository): void
    {
        $this->repository = $repository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $contentId = $input->getArgument('contentId');

        $criterion = new Criterion\ContentId($contentId);

        //$this->repository->getPermissionResolver()->setCurrentUserReference($this->repository->getUserService()->loadUserByLogin('admin'));

        $result = $this->repository->getSearchService()->findSingle($criterion);
        $io->newLine();
        $output->writeln($result->getName());

        $io->success('Done.');

        return 0;
    }
}

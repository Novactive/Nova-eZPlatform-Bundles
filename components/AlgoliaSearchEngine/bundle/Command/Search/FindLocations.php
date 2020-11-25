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
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Just a command to help debugging when contributing.
 */
final class FindLocations extends Command
{
    protected static $defaultName = 'nova:ez:algolia:find:locations';

    /**
     * @var Repository
     */
    private $repository;

    protected function configure(): void
    {
        $this
            ->setHidden(true)
            ->setName(self::$defaultName)
            ->setDescription('Fetch the Locations by Query.');
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

        $query = new LocationQuery();

        $query->filter = new Criterion\Visibility(Criterion\Visibility::VISIBLE);

        $query->query = new Criterion\MatchAll();
        $query->offset = 0;
        $query->limit = 10;
        $query->sortClauses = [new SortClause\Location\Priority()];

        $result = $this->repository->getSearchService()->findLocations($query);

        $io->section('Results:');
        foreach ($result->searchHits as $searchHit) {
            /* @var Location $location */
            $location = $searchHit->valueObject;
            $output->writeln($location->pathString);
        }
        $io->newLine();

        $io->success('Done.');

        return 0;
    }
}

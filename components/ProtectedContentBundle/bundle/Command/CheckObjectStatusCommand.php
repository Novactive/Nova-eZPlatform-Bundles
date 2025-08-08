<?php

declare(strict_types=1);

namespace Novactive\Bundle\eZProtectedContentBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Core\Repository\SiteAccessAware\Repository;
use Novactive\Bundle\eZProtectedContentBundle\Repository\ProtectedAccessRepository;
use Novactive\Bundle\eZProtectedContentBundle\Services\ObjectStateHelper;
use Novactive\Bundle\eZProtectedContentBundle\Services\ProtectedAccessHelper;
use Novactive\Bundle\eZProtectedContentBundle\Services\ReindexHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CheckObjectStatusCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        protected readonly ProtectedAccessHelper     $protectedAccessHelper,
        protected readonly ProtectedAccessRepository $protectedAccessRepository,
        protected readonly ObjectStateHelper         $objectStateHelper,
        protected readonly ReindexHelper             $reindexHelper,
        protected readonly EntityManagerInterface    $entityManager,
        protected readonly Repository                $repository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('novaezprotectedcontent:check_object_status')
            ->setDescription('Vérifie le staus des contenus protégés');
    }

    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        parent::initialize($input, $output);
        $this->io = new SymfonyStyle($input, $output);

        $this->repository->getPermissionResolver()->setCurrentUserReference(
            $this->repository->getUserService()->loadUserByLogin('admin')
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->execute1($input, $output);
        $this->execute2($input, $output);

        return Command::SUCCESS;
    }

    protected function execute1(InputInterface $input, OutputInterface $output)
    {

        $list = $this->protectedAccessRepository->findAll(0, 1000);

        $this->io->comment(sprintf('%d entities to check', count($list)));
        $this->io->newLine();
        $progressBar = $this->io->createProgressBar(count($list));

        foreach ($list as $protectedAccess) {
            $progressBar->advance();

            if ($this->io->isVerbose()) {
                $progressBar->display();
                $this->io->write(sprintf(' - Checking ProtectedAccess %s - ', $protectedAccess->getId()));
            }

            $content = $this->protectedAccessHelper->getContent($protectedAccess);

            if (!$content) {
                $this->io->write(' No content; => DELETE ');
                $this->entityManager->remove($protectedAccess);
                $this->entityManager->flush();
            } else {
                $count = $this->protectedAccessHelper->count($protectedAccess);
                if ($this->io->isVerbose()) {
                    $this->io->write(sprintf(' - %d ProtectedAccess for ContentAndDescendants [%d] "%s" ', $count, $content->id, $content->getName()));
                }

                $this->objectStateHelper->setStatesForContentAndDescendants($content);
                $this->reindexHelper->reindexContent($content);
                if ($protectedAccess->isProtectChildren()) {
                    $this->reindexHelper->reindexChildren($content);
                }
            }

            if ($this->io->isVerbose()) {
                $this->io->newLine();
            }
        }

        $progressBar->finish();
        $this->io->newLine();
    }


    protected function execute2(InputInterface $input, OutputInterface $output)
    {
        $objectStateGroupIdentifier = $this->objectStateHelper->objectStateGroupIdentifier; // 'protected_content'
        $objectStateIdentifier = $this->objectStateHelper->protectedObjectStateIdentifier; // 'protected'

        $group = $this->repository->getObjectStateService()->loadObjectStateGroupByIdentifier($objectStateGroupIdentifier);
        $state = $this->repository->getObjectStateService()->loadObjectStateByIdentifier($group, $objectStateIdentifier);


        $query = new LocationQuery();
        $filters = [];
        $filters[] = new Query\Criterion\ObjectStateIdentifier($state->identifier, $group->identifier);
        $query->limit = 1000;

        $query->filter = new Query\Criterion\LogicalAnd($filters);
        $query->sortClauses = [
            new Query\SortClause\ContentId(),
        ];

        $searchResult = $this->repository->getSearchService()->findContent($query);

        $this->io->comment(sprintf('%d Content to check', $searchResult->totalCount));

        $progressBar = $this->io->createProgressBar($searchResult->totalCount);
        $progressBar->start();
        $this->io->newLine();

        foreach ($searchResult->searchHits as $hit) {
            $progressBar->advance();

            /** @var Content $content */
            $content = $hit->valueObject;


            if ($this->io->isVerbose()) {
                $progressBar->display();
                $this->io->write(sprintf(' - Checking Content [%d] "%s" - ', $content->id, $content->getName()));

                $protectedAccessList = $this->protectedAccessRepository->findByContent($content);
                $hasProtectedAccess = $this->protectedAccessHelper->hasProtectedAccess($content);
                $hasEmailProtectedAccess = $this->protectedAccessHelper->hasEmailProtectedAccess($content);
                $hasPasswordProtectedAccess = $this->protectedAccessHelper->hasPasswordProtectedAccess($content);

                $this->io->write(sprintf(
                    ' - %d protections trouvées. Mot de passe: %s, Email: %s',
                    count($protectedAccessList),
                    $hasPasswordProtectedAccess ? 'Oui' : 'Non',
                    $hasEmailProtectedAccess ? 'Oui' : 'Non',
                ));

                $this->objectStateHelper->setStatesForContent($content);
                $this->reindexHelper->reindexContent($content);
            }

            if ($this->io->isVerbose()) {
                $this->io->newLine();
            }
        }

        $progressBar->finish();
        $this->io->newLine();
    }
}

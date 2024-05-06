<?php

/**
 * NovaeZProtectedContentBundle.
 *
 * @package   Novactive\Bundle\eZProtectedContentBundle
 *
 * @author    Novactive
 * @copyright 2023 Novactive
 * @license   https://github.com/Novactive/eZProtectedContentBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZProtectedContentBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Novactive\Bundle\eZProtectedContentBundle\Entity\ProtectedTokenStorage;
use Novactive\Bundle\eZProtectedContentBundle\Repository\ProtectedTokenStorageRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CleanTokenCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setName('novaezprotectedcontent:cleantoken')
            ->setDescription('Remove expired token in the DB');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var ProtectedTokenStorageRepository $protectedTokenStorageRepository */
        $protectedTokenStorageRepository = $this->entityManager->getRepository(ProtectedTokenStorage::class);

        $entities = $protectedTokenStorageRepository->findExpired();

        foreach ($entities as $entity) {
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();

        $io->success(sprintf('%d entities deleted', count($entities)));
        $io->success('Done.');
        return Command::SUCCESS;
    }
}

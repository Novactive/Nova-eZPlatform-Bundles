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

use Novactive\Bundle\eZProtectedContentBundle\Repository\ProtectedTokenStorageRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CleanTokenCommand extends Command
{
    public function __construct(
        protected ProtectedTokenStorageRepository $protectedTokenStorageRepository,
    ) {
        parent::__construct();
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

        $entities = $this->protectedTokenStorageRepository->findExpired();

        $io->comment(sprintf('%d entities to delete', count($entities)));

        foreach ($entities as $entity) {
            $this->protectedTokenStorageRepository->remove($entity);
        }

        $this->protectedTokenStorageRepository->flush();

        $io->success(sprintf('%d entities deleted', count($entities)));
        $io->success('Done.');

        return Command::SUCCESS;
    }
}

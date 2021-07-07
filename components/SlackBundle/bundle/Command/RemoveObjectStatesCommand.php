<?php

/**
 * NovaeZSlackBundle Bundle.
 *
 * @package   Novactive\Bundle\eZSlackBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZSlackBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZSlackBundle\Command;

use eZ\Publish\API\Repository\Repository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveObjectStatesCommand extends Command
{
    private Repository $repository;

    public function __construct(Repository $firstResponder)
    {
        $this->repository = $firstResponder;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('novaezslack:fixtures:remove:object:states')
            ->setDescription('Remove a publication workflow chain')
            ->setHidden(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $admin = $this->repository->getUserService()->loadUser(14);
        $this->repository->getPermissionResolver()->setCurrentUserReference($admin);

        $allGroups = $this->repository->getObjectStateService()->loadObjectStateGroups();
        foreach ($allGroups as $group) {
            if ('publication_chain' === $group->identifier) {
                $this->repository->getObjectStateService()->deleteObjectStateGroup($group);

                return;
            }
        }
    }
}

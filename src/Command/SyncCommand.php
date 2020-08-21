<?php

/**
 * eZ Platform Bundles Mono Repo Project.
 *
 * @author    Novactive - Sébastien Morel <s.morel@novactive.com> aka Plopix <morel.seb@gmail.com>
 * @copyright 2020 Novactive
 * @license   MIT
 */

declare(strict_types=1);

namespace Novactive\eZPlatform\Bundles\Command;

use Novactive\eZPlatform\Bundles\Core\Collection\Components;
use Novactive\eZPlatform\Bundles\Core\Splitter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SyncCommand extends Command
{
    use AskValidLocaleBranch;

    protected static $defaultName = 'sync';

    protected function configure(): void
    {
        $this->setDescription('Split and Synchronize a branch to its related sub repository');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $branch = $this->askValidLocaleBranch(
            'Please enter the name of the branch to sync',
            'master',
            $input,
            $output
        );

        $answer = $io->ask(
            "Synchronize <fg=yellow>{$branch}</> accross all the components. Continue?"
        );

        if ('yes' !== $answer) {
            $io->comment('Ok. Nothing was done');

            return Command::SUCCESS;
        }

        $splitter = new Splitter();

        $components = (new Components())();

        foreach ($components as $component) {
            $io->section("Component {$component}");
            $splitter($component, $branch);
            $io->success("{$component} has been synced.");
        }

        return Command::SUCCESS;
    }
}

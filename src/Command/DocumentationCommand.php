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
use Novactive\eZPlatform\Bundles\Core\Documenter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

final class DocumentationCommand extends Command
{
    use AskValidLocaleBranch;

    protected static $defaultName = 'doc';

    protected function configure(): void
    {
        $this->setDescription('Generate the documentation based on bundle documentation.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $loader = new FilesystemLoader(__DIR__.'/../../documentation/templates');
        $twig = new Environment($loader, ['debug' => true]);
        $twig->addFunction(
            new TwigFunction(
                'dump',
                function ($var) {
                    $cloner = new VarCloner();
                    $dumper = new HtmlDumper();

                    return $dumper->dump($cloner->cloneVar($var));
                }
            )
        );
        (new Documenter($twig, (new Components())()))('master');

        $io->success('Done.');

        return Command::SUCCESS;
    }
}

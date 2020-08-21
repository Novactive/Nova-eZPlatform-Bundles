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
use Novactive\eZPlatform\Bundles\Core\Collection\RemoteTags;
use Novactive\eZPlatform\Bundles\Core\Tagger;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

final class TagCommand extends Command
{
    use AskValidLocaleBranch;

    protected static $defaultName = 'tag';

    protected function configure(): void
    {
        $this->setDescription('Tag a component');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $components = (new Components())();

        $helper = $this->getHelper('question');
        $question = new Question('Please enter the name of the component to tag : ');
        $question->setAutocompleterValues($components);
        $question->setValidator(
            function ($answer) use ($components) {
                if (!\is_string($answer) || !\in_array($answer, $components, true)) {
                    throw new RuntimeException('This component does not exist.');
                }

                return $answer;
            }
        );
        $component = $helper->ask($input, $output, $question);
        $branch = $this->askValidLocaleBranch(
            'Please enter the name of the branch you want to tag',
            'master',
            $input,
            $output
        );

        $existingTags = array_map(fn ($data) => $data['name'], (new RemoteTags())($component));
        $io->writeln('<comment>Existing tags</comment>: '.implode('<fg=yellow>,</>', $existingTags));

        $tag = $io->ask(
            'Please enter the tag name: ',
            null,
            function ($answer) use ($existingTags) {
                if (!\is_string($answer) || \in_array($answer, $existingTags)) {
                    throw new RuntimeException('This tag already exists.');
                }

                return $answer;
            }
        );

        $message = $io->ask('And the message: ');

        $tagger = new Tagger();
        $tagger($component, $branch, $tag, $message);

        return Command::SUCCESS;
    }
}

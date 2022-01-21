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
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

trait AskValidComponent
{
    private function askValidComponent(
        string $questionStr,
        string $default,
        InputInterface $input,
        OutputInterface $output
    ): string {
        $helper = $this->getHelper('question');
        $question = new Question($questionStr."? <fg=yellow>[$default]</> : ", $default);
        $components = (new Components())();
        $components[] = 'all';
        $question->setAutocompleterValues($components);
        $question->setValidator(
            function ($answer) use ($components) {
                if (!\is_string($answer) || !\in_array($answer, $components)) {
                    throw new RuntimeException('This component does not exist. Do you have it?');
                }

                return $answer;
            }
        );

        return $helper->ask($input, $output, $question);
    }
}

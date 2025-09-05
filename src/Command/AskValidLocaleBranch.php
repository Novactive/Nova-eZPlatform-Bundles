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

use Novactive\eZPlatform\Bundles\Core\Collection\Branches;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

trait AskValidLocaleBranch
{
    private function askValidLocaleBranch(
        string $questionStr,
        string $default,
        InputInterface $input,
        OutputInterface $output
    ): string {
        $helper = $this->getHelper('question');
        $question = new Question($questionStr."? <fg=yellow>[$default]</> : ", $default);
        $availableBranches = (new Branches())();
        $question->setAutocompleterValues($availableBranches);
        $question->setValidator(
            function ($answer) use ($availableBranches) {
                if (!\is_string($answer) || !\in_array($answer, $availableBranches)) {
                    throw new \RuntimeException('This branch does not exist locally. Do you have it?');
                }

                return $answer;
            }
        );

        return $helper->ask($input, $output, $question);
    }
}

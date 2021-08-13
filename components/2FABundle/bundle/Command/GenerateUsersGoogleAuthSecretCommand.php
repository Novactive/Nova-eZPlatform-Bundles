<?php

/**
 * NovaeZ2FABundle.
 *
 * @package   NovaeZ2FABundle
 *
 * @author    Yassine HANINI
 * @copyright 2021 AlmaviaCX
 * @license   https://github.com/Novactive/NovaeZ2FA/blob/main/LICENSE
 */

namespace Novactive\Bundle\eZ2FABundle\Command;

use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class GenerateUsersGoogleAuthSecretCommand extends Command
{
    /**
     * @var GoogleAuthenticator
     */
    private $googleAuthenticator;

    /**
     * @required
     */
    public function setGoogleAuthenticator($googleAuthenticator): self
    {
        $this->googleAuthenticator = $googleAuthenticator;

        return $this;
    }

    protected function configure(): void
    {
        $this
            ->setName('acx:users:generate-google-auth-secret')
            ->setDescription(
                'generate google authentication secret for users'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln($this->googleAuthenticator->generateSecret());

        $io->success('Done.');

        return 0;
    }
}

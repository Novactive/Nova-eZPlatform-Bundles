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
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
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
     * @var TotpAuthenticator
     */
    private $totpAuthenticator;

    /**
     * @required
     */
    public function setAuthenticators(
        GoogleAuthenticator $googleAuthenticator,
        TotpAuthenticator $totpAuthenticator
    ): self {
        $this->googleAuthenticator = $googleAuthenticator;
        $this->totpAuthenticator = $totpAuthenticator;

        return $this;
    }

    protected function configure(): void
    {
        $this
            ->setName('acx:users:generate-auth-secret')
            ->setDescription('generate authentication secret for users')
            ->addArgument('method', InputArgument::REQUIRED, '2FA method: google or totp');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ('google' === $input->getArgument('method')) {
            $io->section($this->googleAuthenticator->generateSecret());
        } elseif ('totp' === $input->getArgument('method')) {
            $io->section($this->totpAuthenticator->generateSecret());
        } else {
            $io->error('Wrong method provided.');
        }

        $io->success('Done.');

        return 0;
    }
}

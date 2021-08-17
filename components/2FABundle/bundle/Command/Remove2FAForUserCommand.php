<?php

/**
 * NovaeZ2FABundle.
 *
 * @package   NovaeZ2FABundle
 *
 * @author    Maxim Strukov <maxim.strukov@almaviacx.com>
 * @copyright 2021 AlmaviaCX
 * @license   https://github.com/Novactive/NovaeZ2FA/blob/main/LICENSE
 */

namespace Novactive\Bundle\eZ2FABundle\Command;

use eZ\Publish\Core\MVC\Symfony\Security\User;
use Novactive\Bundle\eZ2FABundle\Core\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class Remove2FAForUserCommand extends Command
{
    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @required
     */
    public function setAuthenticators(UserProviderInterface $userProvider, UserRepository $userRepository): self
    {
        $this->userProvider = $userProvider;
        $this->userRepository = $userRepository;

        return $this;
    }

    protected function configure(): void
    {
        $this
            ->setName('acx:users:remove-2fa')
            ->setDescription('Removes the 2FA secret key for the specified user')
            ->addArgument('user_login', InputArgument::REQUIRED, 'User Login');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /* @var User $user */
        $user = $this->userProvider->loadUserByUsername($input->getArgument('user_login'));

        $this->userRepository->deleteUserAuthSecrets($user->getAPIUser()->id);

        $io->success('Done.');

        return 0;
    }
}

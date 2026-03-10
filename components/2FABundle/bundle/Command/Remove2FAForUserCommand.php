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

declare(strict_types=1);

namespace Novactive\Bundle\eZ2FABundle\Command;

use Ibexa\Core\MVC\Symfony\Security\User;
use Novactive\Bundle\eZ2FABundle\Core\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\User\UserProviderInterface;

#[AsCommand(
    name: 'nova:2fa:remove-secret-key',
    description: 'Removes the 2FA secret key for the specified user',
)]
final class Remove2FAForUserCommand extends Command
{
    public function __construct(
        private readonly UserProviderInterface $userProvider,
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('user_login', InputArgument::REQUIRED, 'User Login');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var User $user */
        $user = $this->userProvider->loadUserByIdentifier($input->getArgument('user_login'));

        $this->userRepository->deleteUserAuthData($user->getAPIUser()->id);

        $io->success('Done.');

        return Command::SUCCESS;
    }
}

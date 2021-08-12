<?php

/**
 * NovaeZLDAPAuthenticator Bundle.
 *
 * @package   Novactive\Bundle\eZLDAPAuthenticatorBundle
 *
 * @author    Novactive
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZLdapAuthenticatorBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZLDAPAuthenticatorBundle\Command;

use Novactive\eZLDAPAuthenticator\User\EzLdapUser;
use Novactive\eZLDAPAuthenticator\User\Provider\EzLdapUserProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class FindUserCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'nova_ez_ldap:find-user';

    /** @var EzLdapUserProvider */
    protected $userProvider;

    /**
     * @required
     */
    public function setUserProvider(EzLdapUserProvider $userProvider): void
    {
        $this->userProvider = $userProvider;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setDescription('Query LDAP for the specified username')
            ->addArgument('username', InputArgument::REQUIRED, 'Username to search');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument('username');
        if (null === $username) {
            $io->error('Provided username is null');

            return;
        }
        try {
            /** @var EzLdapUser $user */
            $user = $this->userProvider->loadUserByUsername(is_array($username) ? $username[0] : $username);
            $io->success(sprintf('Found user %s (%s)', $user->getUsername(), $user->getEmail()));
        } catch (UsernameNotFoundException $exception) {
            $io->error($exception->getMessage());
        }
    }
}

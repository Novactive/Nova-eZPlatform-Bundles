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

namespace Novactive\Bundle\eZ2FABundle\Core;

use Exception;
use Novactive\Bundle\eZ2FABundle\Entity\UserEmailAuth;
use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Email\Generator\CodeGeneratorInterface;

final class EmailCodeGenerator implements CodeGeneratorInterface
{
    public function __construct(
        protected UserRepository $userRepository,
        protected AuthCodeMailerInterface $mailer,
        protected int $digits
    ) {
    }

    public function generateAndSend(TwoFactorInterface $user): void
    {
        $min = 10 ** ($this->digits - 1);
        $max = 10 ** $this->digits - 1;
        $code = $this->generateCode($min, $max);
        /* @var UserEmailAuth $user */
        $user->setEmailAuthCode((string) $code);
        $this->userRepository->updateEmailAuthenticationCode($user->getAPIUser()->getUserId(), (string) $code);
        $this->mailer->sendAuthCode($user);
    }

    public function reSend(TwoFactorInterface $user): void
    {
        $this->mailer->sendAuthCode($user);
    }

    /**
     * @throws Exception
     */
    private function generateCode(int $min, int $max): int
    {
        return random_int($min, $max);
    }
}

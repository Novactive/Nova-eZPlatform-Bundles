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

use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AuthCodeMailer implements AuthCodeMailerInterface
{
    /**
     * @var Address
     */
    private $senderAddress;

    public function __construct(
        protected MailerInterface $mailer,
        string $senderEmail,
        ?string $senderName,
        protected TranslatorInterface $translator
    ) {
        $this->senderAddress = new Address($senderEmail, $senderName ?? '');
    }

    public function sendAuthCode(TwoFactorInterface $user): void
    {
        $message = new Email();
        $message
            ->to($user->getEmailAuthRecipient())
            ->from($this->senderAddress)
            ->subject($this->translator->trans('email_subject', [], 'novaez2fa'))
            ->text($user->getEmailAuthCode());
        $this->mailer->send($message);
    }
}

<?php

/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Core\Provider;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Novactive\Bundle\eZMailingBundle\Core\DataHandler\Registration;
use Novactive\Bundle\eZMailingBundle\Core\DataHandler\Unregistration;
use Novactive\Bundle\eZMailingBundle\Entity\Campaign;
use Novactive\Bundle\eZMailingBundle\Entity\ConfirmationToken;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use RuntimeException;
use Swift_Message;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class MessageContent
{
    /**
     * @var Environment;
     */
    private $twig;

    /**
     * @var ConfigResolver
     */
    private $configResolver;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        Environment $twig,
        ConfigResolverInterface $configResolver,
        TranslatorInterface $translator
    ) {
        $this->twig = $twig;
        $this->configResolver = $configResolver;
        $this->translator = $translator;
    }

    private function createMessage(string $subject, ?Campaign $campaign = null): Swift_Message
    {
        $prefix = $this->configResolver->getParameter('email_subject_prefix', 'nova_ezmailing');
        $message = new Swift_Message("{$prefix} {$subject}");
        if (null !== $campaign) {
            $message->setFrom($campaign->getSenderEmail(), $campaign->getSenderName());
            $message->setReturnPath($campaign->getReturnPathEmail());

            return $message;
        }
        $message->setFrom(
            $this->configResolver->getParameter('email_from_address', 'nova_ezmailing'),
            $this->configResolver->getParameter('email_from_name', 'nova_ezmailing')
        );
        $message->setReturnPath($this->configResolver->getParameter('email_return_path', 'nova_ezmailing'));

        return $message;
    }

    public function getStartSendingMailing(Mailing $mailing): Swift_Message
    {
        $translated = $this->translator->trans('messages.start_sending.being_sent3', [], 'ezmailing');
        $message = $this->createMessage($translated, $mailing->getCampaign());
        $campaign = $mailing->getCampaign();
        $message->setTo($campaign->getReportEmail());
        $message->setBody(
            $this->twig->render('NovaeZMailingBundle:messages:startsending.html.twig', ['item' => $mailing]),
            'text/html',
            'utf8'
        );

        return $message;
    }

    public function getStopSendingMailing(Mailing $mailing): Swift_Message
    {
        $translated = $this->translator->trans('messages.stop_sending.sent3', [], 'ezmailing');
        $message = $this->createMessage($translated, $mailing->getCampaign());
        $campaign = $mailing->getCampaign();
        $message->setTo($campaign->getReportEmail());
        $message->setBody(
            $this->twig->render('NovaeZMailingBundle:messages:stopsending.html.twig', ['item' => $mailing]),
            'text/html',
            'utf8'
        );

        return $message;
    }

    public function getRegistrationConfirmation(Registration $registration, ConfirmationToken $token): Swift_Message
    {
        $translated = $this->translator->trans('messages.confirm_registration.confirm', [], 'ezmailing');
        $message = $this->createMessage($translated);
        $user = $registration->getUser();
        if (null === $user) {
            throw new RuntimeException('User cannot be empty.');
        }
        $message->setTo($user->getEmail());
        $message->setBody(
            $this->twig->render(
                'NovaeZMailingBundle:messages:confirmregistration.html.twig',
                [
                    'registration' => $registration,
                    'token' => $token,
                ]
            ),
            'text/html',
            'utf8'
        );

        return $message;
    }

    public function getUnregistrationConfirmation(
        Unregistration $unregistration,
        ConfirmationToken $token
    ): Swift_Message {
        $translated = $this->translator->trans('messages.confirm_unregistration.confirmation', [], 'ezmailing');
        $message = $this->createMessage($translated);
        $user = $unregistration->getUser();
        if (null === $user) {
            throw new RuntimeException('User cannot be empty.');
        }
        $message->setTo($user->getEmail());
        $message->setBody(
            $this->twig->render(
                'NovaeZMailingBundle:messages:confirmunregistration.html.twig',
                [
                    'unregistration' => $unregistration,
                    'token' => $token,
                ]
            ),
            'text/html',
            'utf8'
        );

        return $message;
    }
}

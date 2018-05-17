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
use Novactive\Bundle\eZMailingBundle\Core\DataHandler\Registration;
use Novactive\Bundle\eZMailingBundle\Core\DataHandler\Unregistration;
use Novactive\Bundle\eZMailingBundle\Entity\Campaign;
use Novactive\Bundle\eZMailingBundle\Entity\ConfirmationToken;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use RuntimeException;
use Swift_Message;
use Twig_Environment;

/**
 * Class MessageContent.
 */
class MessageContent
{
    /**
     * @var Twig_Environment;
     */
    private $twig;

    /**
     * @var ConfigResolver
     */
    private $configResolver;

    /**
     * MessageContent constructor.
     *
     * @param Twig_Environment $twig
     * @param ConfigResolver   $configResolver
     */
    public function __construct(Twig_Environment $twig, ConfigResolver $configResolver)
    {
        $this->twig           = $twig;
        $this->configResolver = $configResolver;
    }

    /**
     * @param string        $subject
     * @param Campaign|null $campaign
     *
     * @return Swift_Message
     */
    private function createMessage(string $subject, ?Campaign $campaign = null): Swift_Message
    {
        $prefix  = $this->configResolver->getParameter('email_subject_prefix', 'nova_ezmailing');
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

    /**
     * @param Mailing $mailing
     *
     * @return Swift_Message
     */
    public function getStartSendingMailing(Mailing $mailing): Swift_Message
    {
        $message  = $this->createMessage('A new Mailing is being sent', $mailing->getCampaign());
        $campaign = $mailing->getCampaign();
        $message->setTo($campaign->getReportEmail());
        $message->setBody(
            $this->twig->render('NovaeZMailingBundle:messages:startsending.html.twig', ['item' => $mailing]),
            'text/html',
            'utf8'
        );

        return $message;
    }

    /**
     * @param Mailing $mailing
     *
     * @return Swift_Message
     */
    public function getStopSendingMailing(Mailing $mailing): Swift_Message
    {
        $message  = $this->createMessage('A new Mailing has been sent', $mailing->getCampaign());
        $campaign = $mailing->getCampaign();
        $message->setTo($campaign->getReportEmail());
        $message->setBody(
            $this->twig->render('NovaeZMailingBundle:messages:stopsending.html.twig', ['item' => $mailing]),
            'text/html',
            'utf8'
        );

        return $message;
    }

    /**
     * @param Registration      $registration
     * @param ConfirmationToken $token
     *
     * @return Swift_Message
     */
    public function getRegistrationConfirmation(Registration $registration, ConfirmationToken $token): Swift_Message
    {
        $message = $this->createMessage('Confirm your Registration');
        $user    = $registration->getUser();
        if (null === $user) {
            throw new RuntimeException('User cannot be empty.');
        }
        $message->setTo($user->getEmail());
        $message->setBody(
            $this->twig->render(
                'NovaeZMailingBundle:messages:confirmregistration.html.twig',
                [
                    'registration' => $registration,
                    'token'        => $token,
                ]
            ),
            'text/html',
            'utf8'
        );

        return $message;
    }

    /**
     * @param Unregistration    $unregistration
     * @param ConfirmationToken $token
     *
     * @return Swift_Message
     */
    public function getUnregistrationConfirmation(
        Unregistration $unregistration,
        ConfirmationToken $token
    ): Swift_Message {
        $message = $this->createMessage('Confirm your Un-Registration');
        $user    = $unregistration->getUser();
        if (null === $user) {
            throw new RuntimeException('User cannot be empty.');
        }
        $message->setTo($user->getEmail());
        $message->setBody(
            $this->twig->render(
                'NovaeZMailingBundle:messages:confirmunregistration.html.twig',
                [
                    'unregistration' => $unregistration,
                    'token'          => $token,
                ]
            ),
            'text/html',
            'utf8'
        );

        return $message;
    }
}

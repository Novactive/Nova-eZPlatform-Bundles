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

use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
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
     * MessageContent constructor.
     *
     * @param Twig_Environment $twig
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param Mailing $mailing
     *
     * @return Swift_Message
     */
    public function getStartSendingMailing(Mailing $mailing): Swift_Message
    {
        $message  = new Swift_Message('A new Mailing is being sent');
        $campaign = $mailing->getCampaign();
        $message->setFrom($campaign->getSenderEmail(), $campaign->getSenderName());
        $message->setTo($campaign->getReportEmail());
        $message->setBody(
            $this->twig->render('NovaeZMailingBundle:messages:startsending.html.twig', ['item' => $mailing])
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
        $message  = new Swift_Message('A new Mailing has been sent');
        $campaign = $mailing->getCampaign();
        $message->setFrom($campaign->getSenderEmail(), $campaign->getSenderName());
        $message->setTo($campaign->getReportEmail());
        $message->setBody(
            $this->twig->render('NovaeZMailingBundle:messages:stopsending.html.twig', ['item' => $mailing])
        );

        return $message;
    }
}

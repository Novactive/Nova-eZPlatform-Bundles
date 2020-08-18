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

use AppKernel;
use Novactive\Bundle\eZMailingBundle\Core\Modifier\ModifierInterface;
use Novactive\Bundle\eZMailingBundle\Entity\Broadcast as BroadcastEntity;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use Novactive\Bundle\eZMailingBundle\Entity\User as UserEntity;
use Swift_Message;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class MailingContent.
 */
class MailingContent
{
    /**
     * @var array
     */
    protected $nativeContent;

    /**
     * @var ModifierInterface[]
     */
    protected $modifiers;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * MailingContent constructor.
     *
     * @param ModifierInterface[] $modifiers
     */
    public function __construct(iterable $modifiers, RouterInterface $router)
    {
        $this->modifiers = $modifiers;
        $this->router = $router;
    }

    public function preFetchContent(Mailing $mailing): string
    {
        $kernel = new AppKernel('prod', false);
        $client = new Client($kernel);
        $url = $this->router->generate(
            '_novaezmailing_ez_content_view',
            [
                'locationId' => $mailing->getLocation()->id,
                'contentId' => $mailing->getContent()->id,
                'mailingId' => $mailing->getId(),
                'siteaccess' => $mailing->getSiteAccess(),
            ],
            UrlGeneratorInterface::ABSOLUTE_PATH
        );

        $crawler = $client->request('/GET', $url);
        $this->nativeContent[$mailing->getLocationId()] = "<!DOCTYPE html><html>{$crawler->html()}</html>";

        return $this->nativeContent[$mailing->getLocationId()];
    }

    private function getNativeContent(Mailing $mailing): string
    {
        if (!isset($this->nativeContent[$mailing->getLocationId()])) {
            $this->preFetchContent($mailing);
        }

        return $this->nativeContent[$mailing->getLocationId()];
    }

    public function getContentMailing(
        Mailing $mailing,
        UserEntity $recipient,
        BroadcastEntity $broadcast
    ): Swift_Message {
        $html = $this->getNativeContent($mailing);
        foreach ($this->modifiers as $modifier) {
            $html = $modifier->modify($mailing, $recipient, $html, ['broadcast' => $broadcast]);
        }
        $message = new Swift_Message($mailing->getSubject());
        $message->setBody($html, 'text/html', 'utf8');
        $campaign = $mailing->getCampaign();
        $message->setFrom($campaign->getSenderEmail(), $campaign->getSenderName());
        $message->setTo($recipient->getEmail());
        $message->setBcc($campaign->getReportEmail());
        $message->setReturnPath($campaign->getReturnPathEmail());

        return $message;
    }
}

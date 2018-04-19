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

use Novactive\Bundle\eZMailingBundle\Core\Modifier\ModifierInterface;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use Novactive\Bundle\eZMailingBundle\Entity\User as UserEntity;
use Swift_Message;

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
     * MailingContent constructor.
     *
     * @param ModifierInterface[] $modifiers
     */
    public function __construct(iterable $modifiers)
    {
        $this->modifiers = $modifiers;
    }

    /**
     * @param Mailing $mailing
     */
    public function preFetchContent(Mailing $mailing): void
    {
        $this->nativeContent[$mailing->getLocationId()] = 'plop';
    }

    /**
     * @param Mailing $mailing
     *
     * @return string
     */
    private function getNativeContent(Mailing $mailing): string
    {
        if (!isset($this->nativeContent[$mailing->getLocationId()])) {
            $this->preFetchContent($mailing);
        }

        return $this->nativeContent[$mailing->getLocationId()];
    }

    /**
     * @return Swift_Message
     */
    public function getContentMailing(Mailing $mailing, UserEntity $recipient): Swift_Message
    {
        $html = $this->getNativeContent($mailing);
        foreach ($this->modifiers as $modifier) {
            $html = $modifier->modify($mailing, $html);
        }
        $message = new Swift_Message('subject');
        $message->setBody($html);
        $campaign = $mailing->getCampaign();
        $message->setFrom($campaign->getSenderEmail(), $campaign->getSenderName());
        $message->setTo($recipient->getEmail());

        return $message;
    }
}

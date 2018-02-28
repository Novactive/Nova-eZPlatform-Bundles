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

namespace Novactive\Bundle\eZMailingBundle\Core\Mailer;

use Novactive\Bundle\eZMailingBundle\Entity\Campaign;
use Swift_Message;

/**
 * Class Mailing
 */
class Mailing extends Mailer
{

    /**
     * @var Simple
     */
    private $simpleMailer;

    /**
     * Mailing constructor.
     *
     * @param Simple $simpleMailer
     */
    public function __construct(Simple $simpleMailer)
    {
        $this->simpleMailer = $simpleMailer;
    }

    public function sendCampaign(Campaign $campaign)
    {

    }

}

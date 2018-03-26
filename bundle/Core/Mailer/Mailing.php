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

use Swift_Message;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing as MailingEntity;

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

        // get the modifier to track and replace
    }




    public function sendMailing(MailingEntity $mailing)
    {

        // send the report begin message


        // send the mailing


        // send the report end message


    }

}

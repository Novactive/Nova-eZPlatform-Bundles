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

use Swift_Mailer;

/**
 * Class Mailer
 */
class Mailer
{

    /**
     * @var Swift_Mailer
     */
    protected $mailer;

    /**
     * @param Swift_Mailer $mailer
     *
     * @return Mailer
     */
    public function setMailer(Swift_Mailer $mailer): self
    {
        $this->mailer = $mailer;

        return $this;
    }
}

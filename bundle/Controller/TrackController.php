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

namespace Novactive\Bundle\eZMailingBundle\Controller;

use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class TrackController
 * @Route("/t")
 */
class TrackController
{

    /**
     * @Route("/continue/{id}/{salt}/{url}", name="novaezmailing_t_continue")
     */
    public function continueAction(Mailing $mailing, string $salt, string $url)
    {

    }

    /**
     * @Route("/read/{id}/{salt}", name="novaezmailing_t_read")
     */
    public function readAction(Mailing $mailing, $salt)
    {

    }
}

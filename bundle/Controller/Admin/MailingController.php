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

namespace Novactive\Bundle\eZMailingBundle\Controller\Admin;

use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class MailingController.
 *
 * @Route("/mailing")
 */
class MailingController
{
    /**
     * @Route("/show/{mailing}", name="novaezmailing_mailing_show")
     * @Template()
     *
     * @return array
     */
    public function showAction(Mailing $mailing): array
    {
        return [
            'item' => $mailing,
        ];
    }
}

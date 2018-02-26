<?php
/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/eZMailingBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Controller\Admin;

use Novactive\Bundle\eZMailingBundle\Core\Provider\User as UserProvider;
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
     * @Route("/show/{mailing}/{status}/{page}/{limit}", name="novaezmailing_mailing_show",
     *                                              defaults={"page":1, "limit":10, "status":"all"})
     * @Template()
     *
     * @return array
     */
    public function showAction(
        Mailing $mailing,
        UserProvider $provider,
        string $status = 'all',
        int $page = 1,
        int $limit = 10
    ): array {
//        $filers = [
//            'mailingLists' => [$mailing],
//            'status'       => 'all' === $status ? null : (int) $status,
//        ];

        return [
//            'pager'         => $provider->getPagerFilters($filers, $page, $limit),
//            'item'          => $mailingList,
//            'statuses'      => $provider->getStatusesData($filers),
//            'currentStatus' => $status,
        ];
    }
}

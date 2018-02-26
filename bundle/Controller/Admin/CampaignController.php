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
use Novactive\Bundle\eZMailingBundle\Entity\Campaign;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class CampaignController.
 *
 * @Route("/campaing")
 */
class CampaignController
{
    /**
     * @Route("/show/subscriptions/{campaign}/{status}/{page}/{limit}", name="novaezmailing_campaign_subscriptions",
     *                                              defaults={"page":1, "limit":10, "status":"all"})
     * @Template()
     *
     * @return array
     */
    public function subscriptionsAction(
        Campaign $campaign,
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

    /**
     * @Route("/show/mailings/{campaign}/{status}/{page}/{limit}", name="novaezmailing_campaign_mailings",
     *                                              defaults={"page":1, "limit":10, "status":"all"})
     * @Template()
     *
     * @return array
     */
    public function mailingsAction(
        Campaign $campaign,
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

    /**
     * @Route("/show/{campaign}/{status}/{page}/{limit}", name="novaezmailing_campaign_show",
     *                                              defaults={"page":1, "limit":10, "status":"all"})
     * @Template()
     *
     * @return array
     */
    public function showAction(
        Campaign $campaign,
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

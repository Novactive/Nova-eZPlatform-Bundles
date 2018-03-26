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

use Doctrine\ORM\EntityManager;
use eZ\Publish\API\Repository\Repository;
use EzSystems\EzPlatformAdminUi\Tab\LocationView\ContentTab;
use Novactive\Bundle\eZMailingBundle\Core\Provider\User as UserProvider;
use Novactive\Bundle\eZMailingBundle\Entity\Campaign;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class CampaignController.
 *
 * @Route("/campaign")
 */
class CampaignController
{
    /**
     * @Template()
     *
     * @return array
     */
    public function campaignTabsAction(Campaign $campaign, Repository $repository, ContentTab $contentTab): array
    {
        $content     = $campaign->getContent();
        $location    = $repository->getLocationService()->loadLocation(
            $campaign->getContent()->contentInfo->mainLocationId
        );
        $contentType = $repository->getContentTypeService()->loadContentType(
            $campaign->getContent()->contentInfo->contentTypeId
        );
        $preview     = $contentTab->renderView(
            [
                'content'     => $content,
                'location'    => $location,
                'contentType' => $contentType,
            ]
        );

        return [
            'item'    => $campaign,
            'preview' => $preview,
        ];
    }

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
        $filers = [
            'campaign' => $campaign,
            'status'   => 'all' === $status ? null : (int) $status,
        ];

        return [
            'pager'         => $provider->getPagerFilters($filers, $page, $limit),
            'statuses'      => $provider->getStatusesData($filers),
            'currentStatus' => $status,
            'item'          => $campaign,
        ];
    }

    /**
     * @Route("/show/mailings/{campaign}/{status}", name="novaezmailing_campaign_mailings")
     * @Template()
     *
     * @return array
     */
    public function mailingsAction(Campaign $campaign, EntityManager $entityManager, string $status): array
    {
        $repo    = $entityManager->getRepository(Mailing::class);
        $results = $repo->findByFilters(
            [
                'campaign' => $campaign,
                'status'   => $status,
            ]
        );

        return [
            'item'     => $campaign,
            'status'   => $status,
            'children' => $results,
        ];
    }
}

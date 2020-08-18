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

namespace Novactive\Bundle\eZMailingBundle\Listener;

use Doctrine\ORM\EntityManagerInterface;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Tab\Event\TabGroupEvent;
use EzSystems\EzPlatformAdminUi\Tab\TabRegistry;
use Novactive\Bundle\eZMailingBundle\Core\Tab\Campaigns as CampaignsTab;
use Novactive\Bundle\eZMailingBundle\Core\Tab\Mailings as MailingsTab;

/**
 * Class CampaignTab.
 */
class LocationViewGroupTab
{
    /**
     * @var TabRegistry
     */
    private $tabRegistry;

    /**
     * @var CampaignsTab
     */
    private $campaignsTab;

    /**
     * @var MailingsTab
     */
    private $mailingsTab;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * LocationViewGroupTab constructor.
     */
    public function __construct(
        TabRegistry $tabRegistry,
        CampaignsTab $campaignsTab,
        MailingsTab $mailingsTab,
        EntityManagerInterface $entityManager
    ) {
        $this->tabRegistry = $tabRegistry;
        $this->campaignsTab = $campaignsTab;
        $this->mailingsTab = $mailingsTab;
        $this->entityManager = $entityManager;
    }

    public function onTabGroupPreRender(TabGroupEvent $event): void
    {
        $tabGroup = $event->getData();
        if ('location-view' !== $tabGroup->getIdentifier()) {
            return;
        }

        $parameters = $event->getParameters();
        /** @var Location $location */
        $location = $parameters['location'];

        $campaignRepo = $this->entityManager->getRepository('NovaeZMailingBundle:Campaign');
        $campaigns = $campaignRepo->findBy(['locationId' => $location->id]);
        if ($campaigns) {
            $this->campaignsTab->setCampaigns($campaigns);
            $this->tabRegistry->addTab($this->campaignsTab, 'location-view');
            $tabGroup->addTab($this->campaignsTab);
        }

        $mailingRepo = $this->entityManager->getRepository('NovaeZMailingBundle:Mailing');
        $mailings = $mailingRepo->findBy(['locationId' => $location->id]);
        if ($mailings) {
            $this->mailingsTab->setMailings($mailings);
            $this->tabRegistry->addTab($this->mailingsTab, 'location-view');
            $tabGroup->addTab($this->mailingsTab);
        }

        $event->setData($tabGroup);
    }
}

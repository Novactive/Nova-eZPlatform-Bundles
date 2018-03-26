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
use eZ\Publish\API\Repository\Values\Content\Content;
use EzSystems\EzPlatformAdminUi\Tab\Event\TabGroupEvent;
use EzSystems\EzPlatformAdminUi\Tab\TabRegistry;
use Novactive\Bundle\eZMailingBundle\Core\Tab\Campaign as CampaignTab;

/**
 * Class CampaignTab
 */
class LocationViewGroupTab
{
    /**
     * @var TabRegistry
     */
    private $tabRegistry;

    /**
     * @var CampaignTab
     */
    private $campaignTab;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * LocationViewGroupTab constructor.
     *
     * @param TabRegistry            $tabRegistry
     * @param CampaignTab            $campaignTab
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        TabRegistry $tabRegistry,
        CampaignTab $campaignTab,
        EntityManagerInterface $entityManager
    ) {
        $this->tabRegistry   = $tabRegistry;
        $this->campaignTab   = $campaignTab;
        $this->entityManager = $entityManager;
    }

    /**
     * @param TabGroupEvent $event
     */
    public function onTabGroupPreRender(TabGroupEvent $event): void
    {
        $tabGroup = $event->getData();
        if ('location-view' !== $tabGroup->getIdentifier()) {
            return;
        }

        $parameters = $event->getParameters();
        $content    = $parameters['content'];
        /** @var Content $content */
        $campaignRepo = $this->entityManager->getRepository("NovaeZMailingBundle:Campaign");
        $campaigns = $campaignRepo->findBy(['contentId' => $content->id]);

        if ($campaigns) {
            $this->campaignTab->setCampaigns($campaigns);
            $this->tabRegistry->addTab($this->campaignTab, 'location-view');
        }
    }
}

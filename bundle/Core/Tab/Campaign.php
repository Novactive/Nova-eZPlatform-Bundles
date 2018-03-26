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

namespace Novactive\Bundle\eZMailingBundle\Core\Tab;

use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use Novactive\Bundle\eZMailingBundle\Entity\Campaign as CampaignEntity;

/**
 * Class Campaign
 */
class Campaign extends AbstractTab
{
    /**
     * @var CampaignEntity[]
     */
    private $campaigns;

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): string
    {
        return 'novaezmailing-campaign-tab';
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return /** @Desc("Nova eZ Mailing - Campaign Tab") */
            $this->translator->trans('campaign.tab.name', [], 'ezmailing');
    }

    /**
     * {@inheritdoc}
     */
    public function renderView(array $parameters): string
    {
        return "PLOP";
    }

    /**
     * Set the Campaigns
     *
     * @param CampaignEntity[] $campaigns campaigns
     *
     * @return $this
     */
    public function setCampaigns(array $campaigns): self
    {
        $this->campaigns = $campaigns;

        return $this;
    }
}

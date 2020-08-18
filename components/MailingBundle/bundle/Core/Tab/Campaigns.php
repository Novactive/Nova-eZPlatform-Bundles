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
 * Class Campaigns.
 */
class Campaigns extends AbstractTab
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
        return /* @Desc("Nova eZ Mailing - Campaigns Tab") */
            $this->translator->transChoice('campaigns.tab.name', count($this->campaigns), [], 'ezmailing');
    }

    /**
     * {@inheritdoc}
     */
    public function renderView(array $parameters): string
    {
        return $this->twig->render(
            '@NovaeZMailing/admin/tabs/campaigns.html.twig',
            [
                'items' => $this->campaigns,
            ]
        );
    }

    /**
     * Set the Campaigns.
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

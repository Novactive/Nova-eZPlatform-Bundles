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
use Novactive\Bundle\eZMailingBundle\Entity\Mailing as MailingEntity;

class Mailings extends AbstractTab
{
    /**
     * @var MailingEntity[]
     */
    private $mailings;

    public function getIdentifier(): string
    {
        return 'novaezmailing-mailings-tab';
    }

    public function getName(): string
    {
        return /* @Desc("Nova eZ Mailing - Mailings Tab") */
            $this->translator->trans('mailings.tab.name', ['count' => count($this->mailings)], 'ezmailing');
    }

    public function renderView(array $parameters): string
    {
        return $this->twig->render(
            '@NovaeZMailing/admin/tabs/mailings.html.twig',
            [
                'items' => $this->mailings,
            ]
        );
    }

    /**
     * @param MailingEntity[] $mailings
     */
    public function setMailings(array $mailings): self
    {
        $this->mailings = $mailings;

        return $this;
    }
}

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

namespace Novactive\Bundle\eZMailingBundle\Menu;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Novactive\Bundle\eZMailingBundle\Entity\Campaign;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use Novactive\Bundle\eZMailingBundle\Entity\User;
use Novactive\Bundle\eZMailingBundle\Security\Voter\Campaign as CampaignVoter;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class Builder.
 */
class Builder
{
    private $translator;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * Builder constructor.
     *
     * @param FactoryInterface              $factory
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TranslatorInterface           $translator
     */
    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker, TranslatorInterface $translator)
    {
        $this->factory              = $factory;
        $this->authorizationChecker = $authorizationChecker;
        $this->translator           = $translator;
    }

    /**
     * @param RequestStack $requestStack
     *
     * @return ItemInterface
     */
    public function createAdminMenu(RequestStack $requestStack): ItemInterface
    {
        $request      = $requestStack->getMasterRequest();
        $route        = null !== $request ? $request->attributes->get('_route') : null;
        $mailingRoute = 'novaezmailing_mailinglist';
        $userRoute    = 'novaezmailing_user';

        $menu  = $this->factory->createItem('root');
        $child = $menu->addChild(
            'mailinglists',
            ['route' => "{$mailingRoute}_index", 'label' => $this->translator->trans('menu.admin_menu.mailing_lists', [], 'ezmailing')]
        );

        if (substr($route, 0, \strlen($mailingRoute)) === $mailingRoute) {
            $child->setCurrent(true);
        }

        $child = $menu->addChild('users', ['route' => "{$userRoute}_index", 'label' => $this->translator->trans('menu.admin_menu.users', [], 'ezmailing')]);
        if (substr($route, 0, \strlen($userRoute)) === $userRoute) {
            $child->setCurrent(true);
        }

        return $menu;
    }

    /**
     * @param RequestStack           $requestStack
     * @param EntityManagerInterface $entityManager
     *
     * @return ItemInterface
     */
    public function createCampaignMenu(RequestStack $requestStack, EntityManagerInterface $entityManager): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $repo = $entityManager->getRepository(Campaign::class);

        $campaigns       = $repo->findAll();
        $mailingStatuses = Mailing::STATUSES;

        $userRepo    = $entityManager->getRepository(User::class);
        $mailingRepo = $entityManager->getRepository(Mailing::class);
        foreach ($campaigns as $campaign) {
            if (!$this->authorizationChecker->isGranted([CampaignVoter::VIEW], $campaign)) {
                continue;
            }
            $child = $menu->addChild(
                "camp_{$campaign->getId()}",
                [
                    'label' => $campaign->getName(),
                ]
            );

            $count = $userRepo->countByFilters(['campaign' => $campaign]);

            $child->addChild(
                "camp_{$campaign->getId()}_subsciptions",
                [
                    'route'           => 'novaezmailing_campaign_subscriptions',
                    'routeParameters' => ['campaign' => $campaign->getId()],
                    'label'           => $this->translator->trans('menu.campaign_menu.subscriptions', [], 'ezmailing')." ({$count})",
                    'attributes'      => [
                        'class' => 'leaf subscriptions',
                    ],
                ]
            );

            foreach ($mailingStatuses as $status) {
                $count = $mailingRepo->countByFilters(
                    [
                        'campaign' => $campaign,
                        'status'   => $status,
                    ]
                );
                $child->addChild(
                    "mailing_status_{$status}",
                    [
                        'route'           => 'novaezmailing_campaign_mailings',
                        'routeParameters' => [
                            'campaign' => $campaign->getId(),
                            'status'   => $status,
                        ],
                        'label'           => $this->translator->trans('generic.mailing_statuses.'.$status, [], 'ezmailing')." ({$count})",
                        'attributes'      => [
                            'class' => "leaf {$status}",
                        ],
                    ]
                );
            }
        }

        return $menu;
    }

    /**
     * @return ItemInterface
     */
    public function createSaveCancelMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->addChild(
            'novaezmailing_save',
            [
                'label'  => $this->translator->trans('menu.savecancel.save', [], 'ezmailing'),
                'extras' => [
                    'icon' => 'save',
                ],
            ]
        );

        $menu->addChild(
            'novaezmailing_cancel',
            [
                'label'      => $this->translator->trans('menu.savecancel.cancel', [], 'ezmailing'),
                'attributes' => ['class' => 'btn-danger'],
                'extras'     => [
                    'icon' => 'circle-close',
                ],
            ]
        );

        return $menu;
    }
}

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

namespace Novactive\Bundle\eZMailingBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class Builder.
 */
class Builder
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
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
            ['route' => "{$mailingRoute}_index", 'label' => 'Mailing Lists']
        );

        if (substr($route, 0, \strlen($mailingRoute)) === $mailingRoute) {
            $child->setCurrent(true);
        }

        $child = $menu->addChild('users', ['route' => "{$userRoute}_index", 'label' => 'Users']);
        if (substr($route, 0, \strlen($userRoute)) === $userRoute) {
            $child->setCurrent(true);
        }

        return $menu;
    }
}

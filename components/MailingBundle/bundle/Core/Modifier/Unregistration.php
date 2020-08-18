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

namespace Novactive\Bundle\eZMailingBundle\Core\Modifier;

use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use Novactive\Bundle\eZMailingBundle\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class Unregistration.
 */
class Unregistration
{
    /**
     * @var
     */
    private $router;

    /**
     * Tracker constructor.
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function modify(Mailing $mailing, User $user, string $html, array $options = []): string
    {
        $url = $this->router->generate(
            'novaezmailing_registration_remove',
            [
                'email' => $user->getEmail(),
                'siteaccess' => $mailing->getSiteAccess(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $html = str_replace('##UNREGISTER_URL##', $url, $html);

        return $html;
    }
}

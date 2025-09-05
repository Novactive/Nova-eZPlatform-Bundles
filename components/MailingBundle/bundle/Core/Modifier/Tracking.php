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

use Novactive\Bundle\eZMailingBundle\Entity\Broadcast;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use Novactive\Bundle\eZMailingBundle\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class Tracking implements ModifierInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function modify(Mailing $mailing, User $user, string $html, array $options = []): string
    {
        /** @var Broadcast $broadcast */
        $broadcast = $options['broadcast'];
        $uniqId = uniqid('novaezmailing-', true);
        $readUrl = $this->router->generate(
            'novaezmailing_t_read',
            [
                'salt' => $uniqId,
                'broadcastId' => $broadcast->getId(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $readMarker = "<img src=\"{$readUrl}\" width=\"1\" height=\"1\" />";

        $html = str_replace('</body>', "{$readMarker}</body>", $html);

        return preg_replace_callback(
            '/<a(.[^>]*)href="http(s)?(.[^"]*)"/uimx',
            function ($aInput) use ($uniqId, $broadcast) {
                $continueUrl = $this->router->generate(
                    'novaezmailing_t_continue',
                    [
                        'salt' => $uniqId,
                        'broadcastId' => $broadcast->getId(),
                        'url' => base64_encode('http'.trim($aInput[1]).trim($aInput[2]).trim($aInput[3])),
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                return "<a{$aInput[1]}href=\"{$continueUrl}\"";
            },
            $html
        );
    }
}

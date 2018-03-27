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

namespace Novactive\Bundle\eZMailingBundle\Core;

use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class Tracking.
 */
class Tracking
{
    /**
     * @var
     */
    private $router;

    /**
     * Tracker constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function modify(Mailing $mailing, string $html, array $options = []): string
    {
        $uniqId = uniqid('novaezmailing-', true);

        $readUrl    = $this->router->generate(
            'novaezmailing_t_read',
            ['id' => $mailing->getId(), 'salt' => $uniqId],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $readMarker = "<img src=\"{$readUrl}\" width=\"1\" height=\"1\" />";
        $html       = str_replace('</body>', "{$readMarker}</body>", $html);

        return preg_replace_callback(
            '/<a(.[^>]*)href="http(s)?(.[^"]*)"/uimx',
            function ($aInput) use ($mailing, $uniqId) {
                $continueUrl = $this->router->generate(
                    'novaezmailing_t_continue',
                    [
                        'id'   => $mailing->getId(),
                        'salt' => $uniqId,
                        'url'  => base64_encode($aInput[2].$aInput[3]),
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                return "<a{$aInput[1]}href=\"{$continueUrl}\"";
            },
            $html
        );
    }
}

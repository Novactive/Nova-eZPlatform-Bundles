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
 * Class Tracking
 */
class Personalization
{
    /**
     * {@inheritdoc}
     */
    public function modify(Mailing $mailing, string $html, array $options = []): string
    {
        return $html;
    }

}

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

namespace Novactive\Bundle\eZMailingBundle\Twig;

use Symfony\Component\Intl\Intl;
use Twig_SimpleFilter;

/**
 * Class Extension.
 */
class Extension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new Twig_SimpleFilter(
                'country_name',
                function ($value) {
                    return Intl::getRegionBundle()->getCountryName($value);
                }
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals(): array
    {
        return [
            'novaezmailing' => [
                'dateformat' => [
                    'date' => 'short',
                    'time' => 'short',
                ],
            ],
        ];
    }
}

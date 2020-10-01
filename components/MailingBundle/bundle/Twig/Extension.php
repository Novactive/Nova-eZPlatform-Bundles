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

use Symfony\Component\Intl\Countries;
use Twig\Extension\AbstractExtension as TwigExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class Extension extends TwigExtension implements GlobalsInterface
{
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'country_name',
                function ($value) {
                    if (null !== $value) {
                        return Countries::getName($value);
                    }

                    return '';
                }
            ),
        ];
    }

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

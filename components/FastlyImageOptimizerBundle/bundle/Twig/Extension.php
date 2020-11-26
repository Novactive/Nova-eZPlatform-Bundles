<?php

/**
 * Novactive eZ Fastly Image Optimizer Bundle.
 *
 * @author    Novactive <direction.technique@novactive.com>
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZFastlyImageOptimizerBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZFastlyImageOptimizerBundle\Twig;

use Novactive\Bundle\eZFastlyImageOptimizerBundle\Core\AliasGenerator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Extension extends AbstractExtension
{
    /**
     * @var AliasGenerator
     */
    protected $aliasGenerator;

    public function __construct(AliasGenerator $aliasGenerator)
    {
        $this->aliasGenerator = $aliasGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'nova_ezfastlyio_alias',
                [$this->aliasGenerator, 'getVariation'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}

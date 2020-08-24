<?php

/**
 * Novactive eZ Cloudinary Bundle.
 *
 * @package   Novactive\Bundle\eZCloudinary
 *
 * @author    Novactive <novacloudinarybundle@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZCloudinaryBundle/blob/master/LICENSE MIT Licence
 */

namespace Novactive\Bundle\eZCloudinaryBundle\Twig;

use Novactive\Bundle\eZCloudinaryBundle\Core\AliasGenerator;
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
                'nova_ezcloudinary_alias',
                [$this->aliasGenerator, 'getVariation'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function getName(): string
    {
        return 'nova_ezloudinary_extension';
    }
}

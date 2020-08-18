<?php
/**
 * Novactive eZ Cloudinary Bundle
 *
 * @package   Novactive\Bundle\eZCloudinary
 * @author    Novactive <novacloudinarybundle@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZCloudinaryBundle/blob/master/LICENSE MIT Licence
 */

namespace Novactive\Bundle\eZCloudinaryBundle\Twig;

use Twig_Extension;
use Twig_SimpleFunction;
use Novactive\Bundle\eZCloudinaryBundle\Core\AliasGenerator;

/**
 * Class Extension
 */
class Extension extends Twig_Extension
{

    /**
     * @var AliasGenerator
     */
    protected $aliasGenerator;

    /**
     * Extension constructor.
     *
     * @param AliasGenerator $aliasGenerator
     */
    public function __construct(AliasGenerator $aliasGenerator)
    {
        $this->aliasGenerator = $aliasGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction(
                'nova_ezcloudinary_alias', [$this->aliasGenerator, 'getVariation'], ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'nova_ezloudinary_extension';
    }

}

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

declare(strict_types=1);

namespace Novactive\Bundle\eZCloudinaryBundle\Core;

use eZ\Publish\SPI\Variation\Values\ImageVariation as eZImageVariation;

class ImageVariation extends eZImageVariation
{
    /**
     * @var array
     */
    protected $extraTags;
}

<?php

/*
 * NovaeZEnhancedImageAssetBundle.
 *
 * @package   NovaeZEnhancedImageAssetBundle
 *
 * @author    florian
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZEnhancedImageAssetBundle/blob/master/LICENSE
 *
 */

declare(strict_types=1);

namespace Novactive\EzEnhancedImageAsset\Imagine\Filter\Loader;

use Imagine\Image\ImageInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;
use Novactive\EzEnhancedImageAsset\Imagine\Filter\FilterConfiguration;

class ToFormatFilterLoader implements LoaderInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ImageInterface $image, array $options = []): ImageInterface
    {
        /*
         * Actual conversion is done using @see FilterConfiguration::get line 72
         */
        return $image;
    }
}

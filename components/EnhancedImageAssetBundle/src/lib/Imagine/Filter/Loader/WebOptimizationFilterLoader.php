<?php

/**
 * NovaeZEnhancedImageAssetBundle.
 *
 * @package   NovaeZEnhancedImageAssetBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZEnhancedImageAssetBundle/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Novactive\EzEnhancedImageAsset\Imagine\Filter\Loader;

use Imagine\Filter\Basic\WebOptimization;
use Imagine\Image\ImageInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;

class WebOptimizationFilterLoader implements LoaderInterface
{
    /**
     * @inheritDoc
     */
    public function load(ImageInterface $image, array $options = []): ImageInterface
    {
        $filter = new WebOptimization();
        $image = $filter->apply($image);

        return $image;
    }
}

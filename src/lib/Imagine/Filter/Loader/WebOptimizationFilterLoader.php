<?php
/**
 * @copyright Novactive
 * Date: 28/10/19
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
    public function load(ImageInterface $image, array $options = [])
    {
        $filter = new WebOptimization();
        $image  = $filter->apply($image);

        return $image;
    }
}

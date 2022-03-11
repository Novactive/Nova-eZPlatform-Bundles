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

use Imagine\Filter\Basic\Save;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Liip\ImagineBundle\Exception\Imagine\Filter\LoadFilterException;
use Liip\ImagineBundle\Exception\InvalidArgumentException;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;
use Novactive\EzEnhancedImageAsset\Imagine\Filter\FilterConfiguration;
use RuntimeException;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ToFormatFilterLoader implements LoaderInterface
{
    /**
     * @inheritDoc
     */
    public function load(ImageInterface $image, array $options = []): ImageInterface
    {
        /**
         * Actual conversion is done using @see FilterConfiguration::get line 72
         */
        return $image;
    }
}

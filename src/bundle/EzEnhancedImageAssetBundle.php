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

namespace Novactive\EzEnhancedImageAssetBundle;

use Novactive\EzEnhancedImageAsset\FieldValueConverter\FieldValueConverterInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class EzEnhancedImageAssetBundle.
 */
class EzEnhancedImageAssetBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->registerForAutoconfiguration(FieldValueConverterInterface::class)
            ->addTag('ez_enhanced_image_asset.field_value_converter')
        ;
    }
}

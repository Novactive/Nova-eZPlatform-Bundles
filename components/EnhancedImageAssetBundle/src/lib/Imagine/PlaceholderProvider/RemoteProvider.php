<?php

/**
 * NovaeZEnhancedImageAssetBundle.
 *
 * @package   NovaeZEnhancedImageAssetBundle
 *
 * @author    florian
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZEnhancedImageAssetBundle/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Novactive\EzEnhancedImageAsset\Imagine\PlaceholderProvider;

use eZ\Bundle\EzPublishCoreBundle\Imagine\PlaceholderProvider;
use eZ\Publish\Core\FieldType\Image\Value as ImageValue;
use RuntimeException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemoteProvider implements PlaceholderProvider
{
    /**
     * {@inheritdoc}
     */
    public function getPlaceholder(ImageValue $value, array $options = []): string
    {
        $options = $this->resolveOptions($options);

        $path = $this->getTemporaryPath();
        $placeholderUrl = $this->getPlaceholderUrl($options['url_pattern'], $value);

        try {
            $handler = curl_init();

            curl_setopt_array(
                $handler,
                [
                    CURLOPT_URL => $placeholderUrl,
                    CURLOPT_FILE => fopen($path, 'wb'),
                    CURLOPT_TIMEOUT => $options['timeout'],
                    CURLOPT_FAILONERROR => true,
                ]
            );

            if (false === curl_exec($handler)) {
                $msg = "Unable to download placeholder for {$value->id} ($placeholderUrl): ".curl_error($handler);
                throw new RuntimeException($msg);
            }
        } finally {
            curl_close($handler);
        }

        return $path;
    }

    private function resolveOptions(array $options): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(
            [
                'url_pattern' => '',
                'timeout' => 5,
            ]
        );
        $resolver->setAllowedTypes('url_pattern', 'string');
        $resolver->setAllowedTypes('timeout', 'int');

        return $resolver->resolve($options);
    }

    private function getTemporaryPath(): string
    {
        return stream_get_meta_data(tmpfile())['uri'];
    }

    private function getPlaceholderUrl(string $urlPattern, ImageValue $value): string
    {
        $explodedId = explode('/', $value->id);
        foreach ($explodedId as $i => $element) {
            if ($i < 3) {
                continue;
            }
            $explodedId[$i] = rawurlencode($element);
        }

        $imageId = implode('/', $explodedId);

        return strtr(
            $urlPattern,
            [
                '%id%' => $imageId,
                '%width%' => $value->width,
                '%height%' => $value->height,
            ]
        );
    }
}

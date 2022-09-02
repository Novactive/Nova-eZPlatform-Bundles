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

namespace Novactive\EzEnhancedImageAsset\Imagine;

use Ibexa\Bundle\Core\Imagine\PlaceholderProvider;
use Ibexa\Bundle\Core\Imagine\PlaceholderProviderRegistry;
use Ibexa\Core\FieldType\Image\Value as ImageValue;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChainPlaceholderProvider implements PlaceholderProvider
{
    /** @var PlaceholderProviderRegistry */
    protected $providerRegistry;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * ChainPlaceholderProvider constructor.
     */
    public function __construct(PlaceholderProviderRegistry $providerRegistry, LoggerInterface $logger)
    {
        $this->providerRegistry = $providerRegistry;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function getPlaceholder(ImageValue $value, array $options = []): string
    {
        $options = $this->resolveOptions($options);
        $providersConfigs = $options['providers'];
        foreach ($providersConfigs as $providersConfig) {
            $provider = $this->providerRegistry->getProvider($providersConfig['provider']);
            try {
                return $provider->getPlaceholder($value, $providersConfig['options']);
            } catch (RuntimeException $exception) {
                $this->logger->warning($exception->getMessage());
                continue;
            }
        }
        throw new RuntimeException('Unable to get placeholder');
    }

    private function resolveOptions(array $options): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(['providers']);

        return $resolver->resolve($options);
    }
}

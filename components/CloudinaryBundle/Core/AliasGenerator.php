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

use Cloudinary;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\SPI\Variation\Values\Variation;
use eZ\Publish\SPI\Variation\VariationHandler as VariationService;
use Liip\ImagineBundle\Exception\Imagine\Filter\NonExistingFilterException;
use Psr\Log\LoggerInterface;

class AliasGenerator implements VariationService
{
    /**
     * @var ConfigResolverInterface
     */
    protected $configResolver;

    /**
     * @var VariationService
     */
    protected $variationService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        VariationService $variationService,
        ConfigResolverInterface $configResolver,
        LoggerInterface $logger,
        array $auth
    ) {
        $this->configResolver = $configResolver;
        $this->variationService = $variationService;
        $this->logger = $logger;
        Cloudinary::config(
            [
                'cloud_name' => $auth['cloud_name'],
                'api_key' => $auth['api_key'],
                'api_secret' => $auth['api_secret'],
            ]
        );
    }

    /**
     * @param string $variationName
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     *
     * @return ImageVariation|Variation
     */
    public function getVariation(Field $field, VersionInfo $versionInfo, $variationName, array $parameters = [])
    {
        $eZVariationsList = $this->configResolver->getParameter('image_variations');
        $cloudinaryDisabled = $this->configResolver->getParameter('cloudinary_disabled', 'nova_ezcloudinary');

        $cloudinaryVariationsList = [];
        if (!$cloudinaryDisabled) {
            $cloudinaryVariationsList = $this->configResolver->getParameter(
                'cloudinary_variations',
                'nova_ezcloudinary'
            );
        }

        $cloudinaryCompliant = false;
        $eZVariationName = $variationName;

        if (\array_key_exists($variationName, $cloudinaryVariationsList)) {
            $eZVariationName = $cloudinaryVariationsList[$variationName]['ezreference_variation'];
            $cloudinaryCompliant = true;
            if (!\array_key_exists($eZVariationName, $eZVariationsList)) {
                $eZVariationName = 'original';
            }
        }

        try {
            $variation = $this->variationService->getVariation($field, $versionInfo, $eZVariationName);
        } catch (NonExistingFilterException $exception) {
            $cloudinaryFallbackVariation = $this->configResolver->getParameter(
                'cloudinary_fallback_variation',
                'nova_ezcloudinary'
            );
            if ($cloudinaryFallbackVariation) {
                $this->logger->warning(
                    sprintf(
                        'Tried to load variation "%s" which does not exists. Using fallback :"%s". '.
                        'It\'s ok in a dev environment which skip cloudinary variations, '.
                        'but maybe you\'ve missed a variation setting.',
                        $eZVariationName,
                        $cloudinaryFallbackVariation
                    )
                );
                $variation = $this->variationService->getVariation($field, $versionInfo, $cloudinaryFallbackVariation);
            } else {
                throw $exception;
            }
        }

        if (!$cloudinaryCompliant) {
            return $variation;
        }

        $components = parse_url($variation->uri);

        // Make it possible to fetch from Cloudinary
        $proxy = $this->configResolver->getParameter('cloudinary_fecth_proxy', 'nova_ezcloudinary');
        $fetchHost = $proxy['host'];
        $fetchPort = $proxy['port'];

        if (!empty($fetchHost)) {
            $components['host'] = $fetchHost;
        }
        unset($components['port']);
        if (!empty($fetchPort)) {
            $components['port'] = $fetchPort;
        }
        $html = fetch_image_tag(
            $this->unparseUrl($components),
            $cloudinaryVariationsList[$variationName]['filters']
        );
        $attributes = [];
        foreach ($this->parseAttributes($html) as $key => $value) {
            if ('img' === $key) {
                continue;
            }
            if ('src' === $key) {
                $attributes['uri'] = $value;
                continue;
            }
            if ('width' === $key || 'height' === $key) {
                $attributes[$key] = $value;
                continue;
            }
            $attributes['extraTags'][$key] = $value;
        }
        $attributes['extraTags']['coucou'] = 'GO';

        return new ImageVariation($attributes);
    }

    public function parseAttributes(string $tag): array
    {
        $attributes = [];
        $pattern = '#(?(DEFINE)
            (?<name>[a-zA-Z][a-zA-Z0-9-:]*)
            (?<value_double>"[^"]+")
            (?<value_single>\'[^\']+\')
            (?<value_none>[^\s>]+)
            (?<value>((?&value_double)|(?&value_single)|(?&value_none)))
        )
        (?<n>(?&name))(=(?<v>(?&value)))?#xs';
        if (preg_match_all($pattern, $tag, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $attributes[$match['n']] = isset($match['v']) ? trim($match['v'], '\'"') : null;
            }
        }

        return $attributes;
    }

    protected function unparseUrl(array $parsedUrl): string
    {
        $get = function ($key) use ($parsedUrl) {
            return $parsedUrl[$key] ?? null;
        };

        $pass = $get('pass');
        $user = $get('user');
        $userinfo = null !== $pass ? "{$user}:{$pass}" : $user;
        $port = $get('port');
        $scheme = $get('scheme');
        $query = $get('query');
        $fragment = $get('fragment');
        $authority =
            (null !== $userinfo ? "{$userinfo}@" : '').
            $get('host').
            ($port ? ":{$port}" : '');

        return
            (!empty($scheme) ? "{$scheme}:" : '').
            (!empty($authority) ? "//{$authority}" : '').
            $get('path').
            (!empty($query) ? "?{$query}" : '').
            (!empty($fragment) ? "#{$fragment}" : '');
    }
}

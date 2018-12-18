<?php
/**
 * Novactive eZ Cloudinary Bundle
 *
 * @package   Novactive\Bundle\eZCloudinary
 * @author    Novactive <novacloudinarybundle@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZCloudinaryBundle/blob/master/LICENSE MIT Licence
 */

namespace Novactive\Bundle\eZCloudinaryBundle\Core;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Cloudinary;
use eZ\Publish\SPI\Variation\Values\Variation;
use eZ\Publish\SPI\Variation\VariationHandler as VariationService;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;

/**
 * Class AliasGenerator
 */
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
     * AliasGenerator constructor.
     *
     * @param VariationService        $variationService
     * @param ConfigResolverInterface $configResolver
     * @param array                   $auth
     */
    public function __construct(
        VariationService $variationService,
        ConfigResolverInterface $configResolver,
        array $auth
    ) {
        $this->configResolver   = $configResolver;
        $this->variationService = $variationService;
        Cloudinary::config(
            [
                "cloud_name" => $auth['cloud_name'],
                "api_key"    => $auth['api_key'],
                "api_secret" => $auth['api_secret'],
            ]
        );
    }

    /**
     * @param Field       $field
     * @param VersionInfo $versionInfo
     * @param string      $variationName
     * @param array       $parameters
     *
     * @return ImageVariation|Variation
     */
    public function getVariation(Field $field, VersionInfo $versionInfo, $variationName, array $parameters = array ())
    {
        $eZVariationsList         = $this->configResolver->getParameter('image_variations');
        $cloudinaryDisabled       = $this->configResolver->getParameter('cloudinary_disabled', 'nova_ezcloudinary');

        $cloudinaryVariationsList = [];
        if(!$cloudinaryDisabled) {
            $cloudinaryVariationsList = $this->configResolver->getParameter('cloudinary_variations', 'nova_ezcloudinary');
        }

        $cloudinaryCompliant = false;
        $eZVariationName     = $variationName;

        if (array_key_exists($variationName, $cloudinaryVariationsList)) {
            $eZVariationName     = $cloudinaryVariationsList[$variationName]['ezreference_variation'];
            $cloudinaryCompliant = true;
            if (!array_key_exists($eZVariationName, $eZVariationsList)) {
                $eZVariationName = 'original';
            }
        }
        $variation = $this->variationService->getVariation($field, $versionInfo, $eZVariationName);

        if (!$cloudinaryCompliant) {
            return $variation;
        }

        $components = parse_url($variation->uri);

        // Make it possible to fetch from Cloudinary
        $proxy     = $this->configResolver->getParameter('cloudinary_fecth_proxy', 'nova_ezcloudinary');
        $fetchHost = $proxy['host'];
        $fetchPort = $proxy['port'];

        if (!empty($fetchHost)) {
            $components['host'] = $fetchHost;
        }
        unset($components['port']);
        if (!empty($fetchPort)) {
            $components['port'] = $fetchPort;
        }
        $html       = fetch_image_tag(
            $this->unparseUrl($components),
            $cloudinaryVariationsList[$variationName]['filters']
        );
        $attributes = [];
        foreach ($this->parseAttributes($html) as $key => $value) {
            if ($key == 'img') {
                continue;
            }
            if ($key == 'src') {
                $attributes['uri'] = $value;
                continue;
            }
            if ($key == 'width' || $key == 'height') {
                $attributes[$key] = $value;
                continue;
            }
            $attributes['extraTags'][$key] = $value;
        }
        $attributes['extraTags']['coucou'] = "GO";

        return new ImageVariation($attributes);
    }

    /**
     * @param string $tag
     *
     * @return array
     */
    function parseAttributes($tag)
    {
        $attributes = [];
        $pattern    = '#(?(DEFINE)
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

    /**
     * @param $parsedUrl
     *
     * @return string
     */
    protected function unparseUrl(array $parsedUrl)
    {
        $get = function ($key) use ($parsedUrl) {
            return isset($parsedUrl[$key]) ? $parsedUrl[$key] : null;
        };

        $pass      = $get('pass');
        $user      = $get('user');
        $userinfo  = $pass !== null ? "{$user}:{$pass}" : $user;
        $port      = $get('port');
        $scheme    = $get('scheme');
        $query     = $get('query');
        $fragment  = $get('fragment');
        $authority =
            ($userinfo !== null ? "{$userinfo}@" : '').
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

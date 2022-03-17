<?php

/**
 * NovaeZSEOBundle NovaeZSEOExtension.
 *
 * @package   Novactive\Bundle\eZSEOBundle
 *
 * @author    Novactive <novaseobundle@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZSEOBundle/blob/master/LICENSE MIT Licence
 */

namespace Novactive\Bundle\eZSEOBundle\Twig;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Locale\LocaleConverterInterface as LocaleConverter;
use Novactive\Bundle\eZSEOBundle\Core\CustomFallbackInterface;
use Novactive\Bundle\eZSEOBundle\Core\FieldType\Metas\Value as MetasFieldValue;
use Novactive\Bundle\eZSEOBundle\Core\Meta;
use Novactive\Bundle\eZSEOBundle\Core\MetaNameSchema;
use Novactive\Bundle\eZSEOBundle\Service\MetaCompositionService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class NovaeZSEOExtension extends AbstractExtension implements GlobalsInterface
{
    protected MetaCompositionService $metaCompositionService;
    /**
     * Locale Converter.
     *
     * @var LocaleConverter
     */
    protected $localeConverter;

    /**
     * @var ConfigResolverInterface
     */
    protected $configResolver;


    public function __construct(
        MetaCompositionService $metaCompositionService,
        LocaleConverter $localeConverter,
        ConfigResolverInterface $configResolver
    ) {
        $this->metaCompositionService = $metaCompositionService;
        $this->localeConverter = $localeConverter;
        $this->configResolver = $configResolver;
    }



    public function getFilters()
    {
        return [
            new TwigFilter('compute_novaseometas', [$this->metaCompositionService, 'computeMetas']),
            new TwigFilter('getposixlocale_novaseometas', [$this, 'getPosixLocale']),
            new TwigFilter('fallback_novaseometas', [$this->metaCompositionService, 'getFallbackedMetaContent']),
        ];
    }

    public function getPosixLocale(string $eZLocale): ?string
    {
        return $this->localeConverter->convertToPOSIX($eZLocale);
    }

    public function getName()
    {
        return 'novaezseo_extension';
    }

    public function getGlobals(): array
    {
        $identifier = $this->configResolver->getParameter('fieldtype_metas_identifier', 'nova_ezseo');
        $fieldtypeMetas = $this->configResolver->getParameter('fieldtype_metas', 'nova_ezseo');
        $metas = $this->configResolver->getParameter('default_metas', 'nova_ezseo');
        $links = $this->configResolver->getParameter('default_links', 'nova_ezseo');
        $gatracker = $this->configResolver->getParameter('google_gatracker', 'nova_ezseo');
        $anonymizeIp = $this->configResolver->getParameter('google_anonymizeIp', 'nova_ezseo');
        $novaeZseo = [
            'fieldtype_metas_identifier' => $identifier,
            'fieldtype_metas' => $fieldtypeMetas,
            'default_metas' => $metas,
            'default_links' => $links,
            'google_gatracker' => '~' !== $gatracker ? $gatracker : null,
            'google_anonymizeIp' => '~' !== $anonymizeIp ? (bool) $anonymizeIp : true,
        ];

        return ['nova_ezseo' => $novaeZseo];
    }
}

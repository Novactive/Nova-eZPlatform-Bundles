<?php

/**
 * NovaeZExtraBundle TextParsingExtension.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZExtraBundle\Twig;

use Exception;
use Novactive\Bundle\eZExtraBundle\Contracts\RouterAware;
use Novactive\Bundle\eZExtraBundle\Core\Helper\eZ\WrapperFactory;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class TextParsingExtension extends AbstractExtension
{
    use RouterAware;

    /**
     * @var WrapperFactory
     */
    private $wrapperFactory;

    public function __construct(WrapperFactory $wrapperFactory)
    {
        $this->wrapperFactory = $wrapperFactory;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('ctaize', [$this, 'ctaConvert']),
            new TwigFilter('ezlinks', [$this, 'ezLinks'], ['is_safe' => ['html']]),
            new TwigFilter('htmldecode', [$this, 'htmlDecode'], ['is_safe' => ['html']]),
        ];
    }

    public function htmlDecode(string $html): string
    {
        return html_entity_decode($html);
    }

    public function eZLinks(string $string): string
    {
        $fixHtml = static function ($string) {
            $openedDiv = substr_count($string, '<div');
            $closedDiv = substr_count($string, '</div>');
            $diff = $openedDiv - $closedDiv;
            if ($diff > 0) {
                for ($i = 0; $i < $diff; ++$i) {
                    $string .= '</div>';
                }
            }

            return $string;
        };

        $matches = null;
        if (0 === preg_match_all('/ezlocation:\/\/([0-9]*)/ui', $string ?? '', $matches)) {
            return $fixHtml($string);
        }

        $replacements = [];
        foreach ($matches[0] as $index => $matchFound) {
            $contentId = $matches[1][$index];
            try {
                $wrapper = $this->wrapperFactory->createByLocationId($contentId);
                $replacements[$matchFound] = $this->generateRouteWrapper($wrapper);
            } catch (Exception $exception) {
                $replacements[$matchFound] = '/';
            }
        }

        $content = str_replace(array_keys($replacements), array_values($replacements), $string);

        return $fixHtml($content);
    }

    public function ctaConvert(string $ctaLink): string
    {
        if ($this->startsWith('/', $ctaLink)) {
            return $ctaLink;
        }

        try {
            if ($this->startsWith($ctaLink, 'ezcontent://')) {
                $contentId = (int) substr($ctaLink, \strlen('ezcontent://'));
                $wrapper = $this->wrapperFactory->createByContentId($contentId);

                return $this->generateRouteWrapper($wrapper);
            }
            if ($this->startsWith($ctaLink, 'ezlocation://')) {
                $locationId = (int) substr($ctaLink, \strlen('ezlocation://'));
                $wrapper = $this->wrapperFactory->createByLocationId($locationId);

                return $this->generateRouteWrapper($wrapper);
            }
        } catch (Exception $exception) {
            return '/';
        }

        if (preg_match('/^([0-9]*)$/uis', $ctaLink)) {
            return "tel://{$ctaLink}";
        }

        return $ctaLink;
    }

    private function startsWith(string $haystack, string $needle): bool
    {
        return 0 === strpos($haystack, $needle);
    }
}

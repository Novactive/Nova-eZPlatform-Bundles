<?php

/**
 * eZ Platform Bundles Mono Repo Project.
 *
 * @author    Novactive - Sébastien Morel <s.morel@novactive.com> aka Plopix <morel.seb@gmail.com>
 * @copyright 2020 Novactive
 * @license   MIT
 */

declare(strict_types=1);

namespace Novactive\eZPlatform\Bundles\Core\Markdown;

use Knp\Menu\ItemInterface;
use Parsedown;

final class Parser extends Parsedown
{
    private array $localLinks = [];

    private array $localImages = [];

    public function __construct(private ItemInterface $menuPointer)
    {
        $this->menuPointer->setExtra('level', 0);
        $this->menuPointer->setChildrenAttribute('class', 'nav flex-column');
    }

    #[\Override]
    protected function inlineImage($Excerpt): ?array
    {
        $element = parent::inlineImage($Excerpt);

        if (null === $element) {
            return $element;
        }

        $element['element']['attributes']['class'] = 'img-fluid';
        $src = $element['element']['attributes']['src'];

        if (!strpos((string) $src, '://')) {
            $this->localImages[] = $src;
        }

        return $element;
    }

    #[\Override]
    protected function inlineLink($Excerpt)
    {
        $element = parent::inlineLink($Excerpt);

        if (isset($element['element']['attributes']['href'])) {
            $href = $element['element']['attributes']['href'];
            if (!strpos($href, '://') && (str_contains($href, '.md') || !str_contains($href, '.'))) {
                $anchor = strpos($href, '#');
                if ($anchor) {
                    $this->localLinks[] = substr($href, 0, $anchor);
                    $element['element']['attributes']['href'] = substr($href, 0, $anchor).'.html'.
                                                                substr($href, $anchor);

                    return $element;
                }
                $this->localLinks[] = $href;
                $element['element']['attributes']['href'] = $href.'.html';
            }
        }

        return $element;
    }

    #[\Override]
    protected function blockHeader($Line): ?array
    {
        $element = parent::blockHeader($Line);
        if (null === $element) {
            return $element;
        }
        $level = (int) substr((string) $element['element']['name'], 1);

        $value = strip_tags((string) $element['element']['text']);
        $element['element']['attributes']['name'] = md5($value);

        while (null !== $this->menuPointer->getParent() && $this->menuPointer->getExtra('level') !== $level - 1) {
            $this->menuPointer = $this->menuPointer->getParent() ?? $this->menuPointer;
        }

        $child = $this->menuPointer->addChild($value, ['uri' => '#'.$element['element']['attributes']['name']]);
        $child->setExtra('level', $level);
        $child->setAttribute('class', 'nav-item');
        $child->setLinkAttribute('class', 'nav-link');
        $this->menuPointer = $child;

        return $element;
    }

    public function getLocalLinks(): array
    {
        return $this->localLinks;
    }

    public function getLocalImages(): array
    {
        return $this->localImages;
    }

    #[\Override]
    protected function blockFencedCode($Line): ?array
    {
        $element = parent::blockFencedCode($Line);
        if (isset($element['element']['text']['attributes']['class'])) {
            $class = $element['element']['text']['attributes']['class'] ?? '';
            if (str_contains($class, 'language-')) {
                $class = str_replace('language-', ' ', $class);
            }

            $element['element']['text']['attributes']['class'] = $class;
        }

        return $element;
    }
}

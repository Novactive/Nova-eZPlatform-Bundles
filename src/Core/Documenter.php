<?php

/**
 * eZ Platform Bundles Mono Repo Project.
 *
 * @author    Novactive - Sébastien Morel <s.morel@novactive.com> aka Plopix <morel.seb@gmail.com>
 * @copyright 2020 Novactive
 * @license   MIT
 */

declare(strict_types=1);

namespace Novactive\eZPlatform\Bundles\Core;

use Knp\Menu\Matcher\Matcher;
use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;
use Novactive\eZPlatform\Bundles\Core\Markdown\Parser;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;

final class Documenter
{
    private Environment $twig;

    private array $components;

    public function __construct(Environment $twig, array $components)
    {
        $this->twig = $twig;
        $this->components = $components;
    }

    public function __invoke(string $branch): void
    {
        $export = __DIR__.'/../../documentation/export';

        // Main
        $this->generatePage($branch, 'README.md', $export, '/');

        // Components
        foreach ($this->components as $component) {
            $this->generatePage($branch, 'README.md', $export, '/', $component);
        }

        $fs = new Filesystem();
        $fs->copy("{$export}/master/README.md.html", "{$export}/index.html");
    }

    private function renderPage(string $branch, string $source): array
    {
        $template = $this->twig->load('doc.html.twig');
        $factory = new MenuFactory();
        $menu = $factory->createItem('root');
        $markdown = new Parser($menu);

        $mdFileContentLines = file($source);

        // we remove the cartouche
        if (trim($mdFileContentLines[2] ?? '').trim($mdFileContentLines[11] ?? '') === '----'.'----') {
            \array_splice($mdFileContentLines, 2, 9);
        }

        $content = $markdown->text(implode('', $mdFileContentLines));
        $renderer = new ListRenderer(new Matcher());
        $renderedMenu = $renderer->render(
            $menu->getRoot(),
            [
                'compressed' => true,
            ]
        );

        return [
            $template->render(
                [
                    'components' => $this->components,
                    'branch' => $branch,
                    'content' => $content,
                    'menu' => $renderedMenu,
                    'hasMenu' => $menu->hasChildren(),
                ]
            ),
            $markdown->getLocalLinks(),
            $markdown->getLocalImages(),
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.UndefinedVariable)
     */
    private function generatePage(
        string $branch,
        string $filePath,
        string $folder,
        string $from,
        ?string $component = null
    ): void {
        if (null === $component) {
            $markdownFolder = __DIR__.'/../../';
        } else {
            $markdownFolder = __DIR__."/../../components/{$component}";
        }

        $fs = new Filesystem();
        $source = "{$markdownFolder}{$from}{$filePath}";
        if (!$fs->exists($source)) {
            return;
        }
        [$content, $links, $images] = $this->renderPage($branch, $source);

        $filePath = str_replace($markdownFolder, '', $source);

        if (null === $component) {
            $destination = "{$folder}/{$branch}";
        } else {
            $destination = "{$folder}/{$branch}/{$component}";
        }

        $fs->dumpFile("{$destination}/{$filePath}.html", $content);

        $from = rtrim(implode('/', \array_slice(explode('/', $filePath), 0, -1)), '/').'/';

        foreach ($links as $subPage) {
            $this->generatePage(
                $branch,
                trim($subPage, ' ./'),
                $folder,
                $from,
                $component
            );
        }

        foreach ($images as $image) {
            $fs->copy("{$markdownFolder}{$from}{$image}", "{$destination}/{$from}/{$image}");
        }
    }
}

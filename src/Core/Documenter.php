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
        foreach ($this->components as $component) {
            $this->generatePage($component, $branch, 'README.md', __DIR__.'/../../documentation/export');
        }
    }

    private function renderPage(string $branch, string $source): array
    {
        $template = $this->twig->load('doc.html.twig');
        $factory = new MenuFactory();
        $menu = $factory->createItem('root');
        $markdown = new Parser($menu);
        $content = $markdown->text(file_get_contents($source));
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
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.UndefinedVariable)
     */
    private function generatePage(string $component, string $branch, string $filePath, string $folder): void
    {
        $componentFolder = __DIR__."/../../components/{$component}";
        $fs = new Filesystem();
        $source = "{$componentFolder}/{$filePath}";
        if (!$fs->exists($source)) {
            return;
        }
        [$content, $links] = $this->renderPage($branch, $source);

        $filePath = str_replace($componentFolder, '', $source);
        $fs->dumpFile(
            "{$folder}/{$branch}/{$component}/{$filePath}.html",
            $content
        );

        foreach ($links as $subPage) {
            $this->generatePage(
                $component,
                $branch,
                trim($subPage, ' ./'),
                $folder
            );
        }
    }
}

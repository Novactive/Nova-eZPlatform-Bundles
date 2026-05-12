<?php

declare(strict_types=1);

namespace Novactive\StaticTemplates\Templating\Twig;

use Twig\Loader\LoaderInterface;
use Twig\Source;

class Loader implements LoaderInterface
{
    /**
     * @var \Twig\Loader\FilesystemLoader
     */
    private $innerFilesystemLoader;

    public function __construct(LoaderInterface $innerFilesystemLoader)
    {
        $this->innerFilesystemLoader = $innerFilesystemLoader;
    }

    public function getCacheKey(string $name): string
    {
        return $this->innerFilesystemLoader->getCacheKey($name);
    }

    public function isFresh(string $name, int $time): bool
    {
        return $this->innerFilesystemLoader->isFresh($name, $time);
    }

    public function exists(string $name): bool
    {
        return $this->innerFilesystemLoader->exists($name);
    }

    public function getSourceContext(string $name): Source
    {
        $source = $this->innerFilesystemLoader->getSourceContext($name);

        return $this->replaceVarComments($source);
    }

    public function replaceVarComments(Source $source): Source
    {
        $code = $source->getCode();
        preg_match_all('/{# @fake ([^\s]+) (.+) #}/', $code, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $code = str_replace(
                $match[0],
                sprintf(
                    '{%% set %s = %s is defined ? %s : generateFake("%s") %%}',
                    $match[1],
                    $match[1],
                    $match[1],
                    addslashes($match[2])
                ),
                $code
            );
        }

        return new Source($code, $source->getName(), $source->getPath());
    }
}

<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\File;

use Ibexa\Core\IO\Flysystem\PathPrefixer\PathPrefixerInterface;

class PathPrefixer implements PathPrefixerInterface
{
    public function __construct(
        protected string $prefix,
        protected string $separator = DIRECTORY_SEPARATOR
    ) {
    }

    protected function getPrefix(): string
    {
        return $this->prefix;
    }

    public function prefixPath(string $path): string
    {
        $prefix = rtrim($this->prefix, '\\/');
        if ('' !== $prefix || $this->prefix === $this->separator) {
            $prefix .= $this->separator;
        }

        return $prefix.ltrim($path, '\\/');
    }

    public function stripPrefix(string $path): string
    {
        return substr($path, strlen($this->prefix));
    }

    public function stripDirectoryPrefix(string $path): string
    {
        return rtrim($this->stripPrefix($path), '\\/');
    }

    public function prefixDirectoryPath(string $path): string
    {
        $prefixedPath = $this->prefixPath(rtrim($path, '\\/'));

        if ('' === $prefixedPath || str_ends_with($prefixedPath, $this->separator)) {
            return $prefixedPath;
        }

        return $prefixedPath.$this->separator;
    }
}

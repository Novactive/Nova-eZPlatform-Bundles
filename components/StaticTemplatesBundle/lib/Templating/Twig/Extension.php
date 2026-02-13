<?php

declare(strict_types=1);

namespace Novactive\StaticTemplates\Templating\Twig;

use Ibexa\Core\MVC\Symfony\SiteAccess;
use Novactive\StaticTemplates\Faker\Generator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Extension extends AbstractExtension
{
    /**
     * @var \Novactive\StaticTemplates\Faker\Generator
     */
    private $generator;

    /**
     * @var \Ibexa\Core\MVC\Symfony\SiteAccess
     */
    private $siteAccess;

    public function setSiteAccess(SiteAccess $siteAccess): void
    {
        $this->siteAccess = $siteAccess;
    }

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('generateFake', [$this, 'generateFake'], ['is_safe' => ['html']]),
        ];
    }

    public function generateFake(string $type)
    {
        if (preg_match('/^static_/', $this->siteAccess->name)) {
            return $this->generator->generate($type);
        }

        return null;
    }
}

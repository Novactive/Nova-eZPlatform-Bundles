<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Twig;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Extension extends AbstractExtension
{
    public function __construct(
        protected RouterInterface $router
    ) {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('imported_content_url', [$this, 'getContentUrl']),
        ];
    }

    public function getContentUrl(int $contentId): ?string
    {
        try {
            return $this->router->generate(
                UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
                ['contentId' => $contentId]
            );
        } catch (NotFoundException $exception) {
            return null;
        }
    }
}

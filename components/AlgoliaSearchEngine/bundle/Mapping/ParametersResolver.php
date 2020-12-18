<?php

/**
 * Nova eZ Algolia Search Engine.
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @licence   "SEE FULL LICENSE OPTIONS IN LICENSE.md"
 *            Nova eZ Algolia Search Engine is tri-licensed, meaning you must choose one of three licenses to use:
 *                - Commercial License: a paid license, meant for commercial use. The default option for most users.
 *                - Creative Commons Non-Commercial No-Derivatives: meant for trial and non-commercial use.
 *                - GPLv3 License: meant for open-source projects.
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Mapping;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Novactive\Bundle\eZAlgoliaSearchEngine\DependencyInjection\Configuration;

final class ParametersResolver
{
    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public static function getReplicas(array $attributes): array
    {
        $replicas = [];
        foreach ($attributes as $attribute) {
            $replicas[] = [
                'key' => "sort_by_{$attribute}_asc",
                'condition' => "asc({$attribute})",
            ];
            $replicas[] = [
                'key' => "sort_by_{$attribute}_desc",
                'condition' => "desc({$attribute})",
            ];
        }

        return $replicas;
    }

    public function ifContentTypeAllowed(string $contentTypeIdentifier): bool
    {
        $excludeContentTypes = $this->configResolver->getParameter('exclude_content_types', Configuration::NAMESPACE);
        $includeContentTypes = $this->configResolver->getParameter('include_content_types', Configuration::NAMESPACE);
        if (
            \count($includeContentTypes) > 0 &&
            !\in_array($contentTypeIdentifier, $includeContentTypes, true)
        ) {
            return false;
        }

        if (
            \count($excludeContentTypes) > 0 &&
            \in_array($contentTypeIdentifier, $excludeContentTypes, true)
        ) {
            return false;
        }

        return true;
    }
}

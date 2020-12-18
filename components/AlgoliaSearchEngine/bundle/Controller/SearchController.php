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

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Controller;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Ibexa\Platform\Search\View\SearchView;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\AlgoliaClient;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\Search\SearchQueryFactory;
use Novactive\Bundle\eZAlgoliaSearchEngine\DependencyInjection\Configuration;
use Novactive\Bundle\eZAlgoliaSearchEngine\Mapping\ParametersResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SearchController extends AbstractController
{
    public function searchAction(
        SearchView $view,
        SearchQueryFactory $searchQueryFactory,
        SerializerInterface $serializer,
        ConfigResolverInterface $configResolver,
        AlgoliaClient $algoliaClient,
        TranslatorInterface $translator
    ): SearchView {
        $query = $searchQueryFactory->create();

        $query->setFacets(
            array_map(
                static function ($attribute) use ($translator) {
                    return [
                        'key' => $attribute,
                        'label' => $translator->trans("facet.{$attribute}", [], 'novaezalgolia'),
                    ];
                },
                $query->getFacets()
            )
        );

        $view->addParameters(
            [
                'query' => $serializer->serialize($query, 'json'),
                'replicas' => array_map(
                    static function ($item) use ($translator) {
                        $item['label'] = $translator->trans($item['key'], [], 'novaezalgolia');

                        return $item;
                    },
                    ParametersResolver::getReplicas(
                        $configResolver->getParameter(
                            'attributes_for_replicas',
                            Configuration::NAMESPACE
                        )
                    )
                ),
                'config' => [
                    'index_name_prefix' => $configResolver->getParameter('index_name_prefix', Configuration::NAMESPACE),
                    'app_id' => $configResolver->getParameter('app_id', Configuration::NAMESPACE),
                    'api_key' => $algoliaClient->getSecuredApiKey(),
                ],
            ]
        );

        return $view;
    }
}

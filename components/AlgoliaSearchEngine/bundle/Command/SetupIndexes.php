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

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Command;

use Algolia\AlgoliaSearch\SearchIndex;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\AlgoliaClient;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\AttributeGenerator;
use Novactive\Bundle\eZAlgoliaSearchEngine\DependencyInjection\Configuration;
use Novactive\Bundle\eZAlgoliaSearchEngine\Mapping\ParametersResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SetupIndexes extends Command
{
    protected static $defaultName = 'nova:ez:algolia:indexes:setup';

    /**
     * @var AlgoliaClient
     */
    private $client;

    /**
     * @var AttributeGenerator
     */
    private $attributeGenerator;

    /**
     * @var LanguageService
     */
    private $languageService;

    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Set up Algolia Indexes.');
    }

    /**
     * @required
     */
    public function setDependencies(
        AlgoliaClient $client,
        AttributeGenerator $attributeGenerator,
        LanguageService $languageService,
        ConfigResolverInterface $configResolver
    ): void {
        $this->client = $client;
        $this->attributeGenerator = $attributeGenerator;
        $this->languageService = $languageService;
        $this->configResolver = $configResolver;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $customSearchableattributes = $this->attributeGenerator->getCustomSearchableAttributes();

        foreach ($this->languageService->loadLanguages() as $language) {
            $replicas = ParametersResolver::getReplicas(
                $this->configResolver->getParameter(
                    'attributes_for_replicas',
                    Configuration::NAMESPACE
                )
            );

            $attributesForFaceting = array_merge(
                array_map(
                    static function ($item) {
                        return "filterOnly({$item})";
                    },
                    $customSearchableattributes
                ),
                $this->configResolver->getParameter(
                    'attributes_for_faceting',
                    Configuration::NAMESPACE
                )
            );

            $settings = [
                'searchableAttributes' => array_merge(
                    $customSearchableattributes,
                    $this->configResolver->getParameter(
                        'searchable_attributes',
                        Configuration::NAMESPACE
                    )
                ),
                'attributesForFaceting' => $attributesForFaceting,
                'attributesToRetrieve' => ['*'],
            ];

            ($this->client)(
                function (SearchIndex $index) use ($replicas, $settings, $io) {
                    $io->section('Index '.$index->getIndexName().' created.');
                    $index->setSettings(
                        array_merge(
                            $settings,
                            [
                                'replicas' => array_map(
                                    static function (string $suffix) use ($index) {
                                        return "{$index->getIndexName()}-{$suffix}";
                                    },
                                    array_column($replicas, 'key')
                                ),
                            ],
                        ),
                        ['forwardToReplicas' => true]
                    );
                },
                $language->languageCode,
                AlgoliaClient::CLIENT_ADMIN_MODE
            );

            foreach ($replicas as $replicaItem) {
                ($this->client)(
                    function (SearchIndex $index) use ($io, $settings, $replicaItem) {
                        $io->writeln('Replica '.$index->getIndexName().' set');
                        $index->setSettings(
                            array_merge(
                                $settings,
                                [
                                    'ranking' => array_merge(
                                        [$replicaItem['condition']],
                                        [
                                            'typo',
                                            'words',
                                            'proximity',
                                            'attribute',
                                            'exact',
                                        ]
                                    ),
                                ]
                            )
                        );
                    },
                    $language->languageCode,
                    AlgoliaClient::CLIENT_ADMIN_MODE,
                    $replicaItem['key']
                );
            }
        }

        $io->success('Done.');

        return 0;
    }
}

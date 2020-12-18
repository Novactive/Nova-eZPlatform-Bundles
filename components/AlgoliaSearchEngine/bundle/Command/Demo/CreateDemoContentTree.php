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

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Command\Demo;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\FieldType\Image;
use Faker\Factory;
use Novactive\Bundle\eZExtraBundle\Core\Manager\eZ\Content as ContentManager;
use Novactive\Bundle\eZExtraBundle\Core\Manager\eZ\ContentType as ContentTypeManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @SuppressWarnings(PHPMD)
 */
class CreateDemoContentTree extends Command
{
    protected static $defaultName = 'nova:ez:algolia:demo:create:contenttree';

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var ContentTypeManager
     */
    private $contentTypeManager;

    /**
     * @var ContentManager
     */
    private $contentManager;

    /**
     * @var SymfonyStyle
     */
    private $io;

    private const CONTENT_TYPE_PREFIX = 'nova_';

    /**
     * @required
     */
    public function setDependencies(
        Repository $repository,
        ContentManager $content,
        ContentTypeManager $contentType
    ): void {
        $this->repository = $repository;
        $this->contentTypeManager = $contentType;
        $this->contentManager = $content;
    }

    protected function configure(): void
    {
        $this
            ->setHidden(true)
            ->setName(self::$defaultName)
            ->setDescription('Create a fake Content Treee.')
            ->addArgument('parentLocationId', InputArgument::REQUIRED, 'Where to create that fake Content Tree');
    }

    private function contentTypes(): array
    {
        return [
            'novel' => [
                'type' => [
                    'nameSchema' => '<title>',
                    'isContainer' => true,
                    'name' => 'Novel',
                ],
                'fields' => [
                    'title' => [
                        'type' => 'ezstring',
                        'name' => 'Title',
                        'isRequired' => true,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                    ],
                    'author' => [
                        'type' => 'ezstring',
                        'name' => 'Author',
                        'isRequired' => true,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                    ],
                    'annotation' => [
                        'type' => 'ezrichtext',
                        'name' => 'Annotation',
                        'isRequired' => false,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                    ],
                    'description' => [
                        'type' => 'ezrichtext',
                        'name' => 'Description',
                        'isRequired' => false,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                    ],
                    'price' => [
                        'type' => 'ezfloat',
                        'name' => 'Price',
                        'isRequired' => true,
                        'isSearchable' => false,
                        'isTranslatable' => true,
                    ],
                    'image' => [
                        'type' => 'ezimage',
                        'name' => 'Cover',
                        'isRequired' => false,
                        'isSearchable' => false,
                        'isTranslatable' => true,
                    ],
                ],
            ],
            'film' => [
                'type' => [
                    'nameSchema' => '<title>',
                    'isContainer' => true,
                    'name' => 'Film',
                ],
                'fields' => [
                    'title' => [
                        'type' => 'ezstring',
                        'name' => 'Title',
                        'isRequired' => true,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                    ],
                    'description' => [
                        'type' => 'ezrichtext',
                        'name' => 'Description',
                        'isRequired' => false,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                    ],
                    'director' => [
                        'type' => 'ezstring',
                        'name' => 'Director',
                        'isRequired' => true,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                    ],
                    'short_plot' => [
                        'type' => 'ezrichtext',
                        'name' => 'Short Plot',
                        'isRequired' => false,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                    ],
                    'box_office' => [
                        'type' => 'ezfloat',
                        'name' => 'Box Office',
                        'isRequired' => true,
                        'isSearchable' => false,
                        'isTranslatable' => true,
                    ],
                    'image' => [
                        'type' => 'ezimage',
                        'name' => 'Poster',
                        'isRequired' => false,
                        'isSearchable' => false,
                        'isTranslatable' => true,
                    ],
                ],
            ],
            'album' => [
                'type' => [
                    'nameSchema' => '<title>',
                    'isContainer' => true,
                    'name' => 'Album',
                ],
                'fields' => [
                    'title' => [
                        'type' => 'ezstring',
                        'name' => 'Title',
                        'isRequired' => true,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                    ],
                    'description' => [
                        'type' => 'ezrichtext',
                        'name' => 'Description',
                        'isRequired' => false,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                    ],
                    'musician' => [
                        'type' => 'ezstring',
                        'name' => 'Musician',
                        'isRequired' => false,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                    ],
                    'songs' => [
                        'type' => 'ezrichtext',
                        'name' => 'Songs',
                        'isRequired' => false,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                    ],
                    'price' => [
                        'type' => 'ezfloat',
                        'name' => 'Price',
                        'isRequired' => true,
                        'isSearchable' => false,
                        'isTranslatable' => true,
                    ],
                    'image' => [
                        'type' => 'ezimage',
                        'name' => 'Cover',
                        'isRequired' => false,
                        'isSearchable' => false,
                        'isTranslatable' => true,
                    ],
                ],
            ],
            'program' => [
                'type' => [
                    'nameSchema' => '<short_title|title>',
                    'isContainer' => true,
                    'name' => 'Program',
                ],
                'fields' => [
                    'title' => [
                        'type' => 'ezstring',
                        'name' => 'Title',
                        'isRequired' => true,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                    ],
                    'description' => [
                        'type' => 'ezrichtext',
                        'name' => 'Description',
                        'isRequired' => false,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                    ],
                    'short_title' => [
                        'type' => 'ezstring',
                        'name' => 'Short Title',
                        'isRequired' => false,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                    ],
                    'intro' => [
                        'type' => 'ezrichtext',
                        'name' => 'Introduction',
                        'isRequired' => false,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                    ],
                    'body' => [
                        'type' => 'ezrichtext',
                        'name' => 'Body',
                        'isRequired' => true,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                    ],
                    'price' => [
                        'type' => 'ezfloat',
                        'name' => 'Price',
                        'isRequired' => true,
                        'isSearchable' => false,
                        'isTranslatable' => true,
                    ],
                    'image' => [
                        'type' => 'ezimage',
                        'name' => 'Image',
                        'isRequired' => false,
                        'isSearchable' => false,
                        'isTranslatable' => true,
                    ],
                ],
            ],
            'tv_show' => [
                'type' => [
                    'nameSchema' => '<title>',
                    'isContainer' => true,
                    'name' => 'TV Show',
                ],
                'fields' => [
                    'title' => [
                        'type' => 'ezstring',
                        'name' => 'Title',
                        'isRequired' => true,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                    ],
                    'host' => [
                        'type' => 'ezstring',
                        'name' => 'Host',
                        'isRequired' => true,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                    ],
                    'description' => [
                        'type' => 'ezrichtext',
                        'name' => 'Description',
                        'isRequired' => false,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                    ],
                    'feedback' => [
                        'type' => 'ezrichtext',
                        'name' => 'Feedback',
                        'isRequired' => true,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                    ],
                    'rating' => [
                        'type' => 'ezfloat',
                        'name' => 'Rating',
                        'isRequired' => true,
                        'isSearchable' => false,
                        'isTranslatable' => true,
                    ],
                    'image' => [
                        'type' => 'ezimage',
                        'name' => 'Image',
                        'isRequired' => false,
                        'isSearchable' => false,
                        'isTranslatable' => true,
                    ],
                ],
            ],
        ];
    }

    private function wrapRichText(string $text): string
    {
        return trim(
            '<?xml version="1.0" encoding="UTF-8"?>
                <section 
                    xmlns="http://docbook.org/ns/docbook" 
                    xmlns:xlink="http://www.w3.org/1999/xlink" 
                    xmlns:ezxhtml="http://ez.no/xmlns/ezpublish/docbook/xhtml" 
                    xmlns:ezcustom="http://ez.no/xmlns/ezpublish/docbook/custom" 
                    version="5.0-variant ezpublish-1.0"
                >
                    <para>'.$text.'</para>
            </section>'
        );
    }

    private function createUpdateContentTypes(): void
    {
        foreach ($this->contentTypes() as $contentTypeIdentifier => $contentTypeInfo) {
            $contentTypeData = $contentTypeInfo['type'];

            $contentTypeData['names'] = ['eng-GB' => $contentTypeData['name']];
            $contentTypeData['descriptions'] = ['eng-GB' => ''];
            $contentTypeData['urlAliasSchema'] = $contentTypeData['nameSchema'];
            unset($contentTypeData['name']);

            $contentTypeFieldDefinitionsData = [];
            $position = 1;
            foreach ($contentTypeInfo['fields'] as $identifier => $fieldInfo) {
                $fieldInfo['names'] = ['eng-GB' => $fieldInfo['name']];
                $fieldInfo['descriptions'] = ['eng-GB' => ''];
                $fieldInfo['identifier'] = $identifier;
                $fieldInfo['settings'] = [];
                $fieldInfo['fieldGroup'] = 'Content';
                $fieldInfo['position'] = $position++;
                unset($fieldInfo['name']);
                $contentTypeFieldDefinitionsData[] = $fieldInfo;
            }

            $this->contentTypeManager->createUpdateContentType(
                self::CONTENT_TYPE_PREFIX.$contentTypeIdentifier,
                'Content',
                $contentTypeData,
                $contentTypeFieldDefinitionsData
            );
            $this->io->progressAdvance(1);
        }
    }

    private function createUpdateContents(int $parentLocationId = 2, int $limit = 30): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < $limit; ++$i) {
            $contents = [
                [
                    'type' => 'novel',
                    'fields' => [
                        'title' => $faker->sentence(3),
                        'description' => $this->wrapRichText($faker->sentence(10)),
                        'author' => $faker->sentence(2),
                        'annotation' => $this->wrapRichText($faker->sentence(10)),
                        'price' => $faker->randomFloat(2, 1, 9),
                    ],
                    'imageField' => 'image',
                ],
                [
                    'type' => 'film',
                    'fields' => [
                        'title' => $faker->sentence(3),
                        'description' => $this->wrapRichText($faker->sentence(10)),
                        'director' => $faker->sentence(2),
                        'short_plot' => $this->wrapRichText($faker->sentence(10)),
                        'box_office' => $faker->randomFloat(2, 1000000, 9999999),
                    ],
                    'imageField' => 'image',
                ],
                [
                    'type' => 'album',
                    'fields' => [
                        'title' => $faker->sentence(3),
                        'description' => $this->wrapRichText($faker->sentence(10)),
                        'musician' => $faker->sentence(2),
                        'songs' => $this->wrapRichText($faker->sentence(10)),
                        'price' => $faker->randomFloat(2, 10, 99),
                    ],
                    'imageField' => 'image',
                ],
                [
                    'type' => 'program',
                    'fields' => [
                        'title' => $faker->sentence(3),
                        'description' => $this->wrapRichText($faker->sentence(10)),
                        'short_title' => $faker->sentence(2),
                        'intro' => $this->wrapRichText($faker->sentence(10)),
                        'body' => $this->wrapRichText($faker->sentence(20)),
                        'price' => $faker->randomFloat(2, 10, 99),
                    ],
                    'imageField' => 'image',
                ],
                [
                    'type' => 'tv_show',
                    'fields' => [
                        'title' => $faker->sentence(3),
                        'host' => $faker->sentence(2),
                        'description' => $this->wrapRichText($faker->sentence(10)),
                        'feedback' => $this->wrapRichText($faker->sentence(15)),
                        'rating' => $faker->randomFloat(1, 1, 5),
                    ],
                    'imageField' => 'image',
                ],
            ];

            foreach ($contents as $content) {
                $filePath = $faker->image();
                $imageValue = new Image\Value(
                    [
                        'path' => $filePath,
                        'fileSize' => filesize($filePath),
                        'fileName' => basename($filePath),
                        'alternativeText' => $faker->sentence(1),
                    ]
                );
                $this->contentManager->createUpdateContent(
                    self::CONTENT_TYPE_PREFIX.$content['type'],
                    $parentLocationId,
                    array_merge($content['fields'], [$content['imageField'] => $imageValue]),
                    uniqid($content['type'].'-', false)
                );
                unlink($filePath);
                $this->io->progressAdvance(1);
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $contentCount = 50;
        $this->io->progressStart(\count($this->contentTypes()) + \count($this->contentTypes()) * $contentCount);
        $this->createUpdateContentTypes();

        $parentLocation = (int) $input->getArgument('parentLocationId');

        // deleting all previously created contents
        $rootLocation = $this->repository->getLocationService()->loadLocation($parentLocation);
        foreach ($this->repository->getLocationService()->loadLocationChildren($rootLocation) as $childLocation) {
            if (
                \in_array(
                    $childLocation->getContent()->getContentType()->identifier,
                    array_map(
                        static function ($item) {
                            return self::CONTENT_TYPE_PREFIX.$item;
                        },
                        ['novel', 'film', 'album', 'program', 'tv_show']
                    ),
                    true
                )
            ) {
                $this->repository->getLocationService()->deleteLocation($childLocation);
            }
        }

        // pass the location id of the top location from input
        $this->createUpdateContents($parentLocation, $contentCount);

        $this->io->progressFinish();

        return Command::SUCCESS;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->repository->getPermissionResolver()->setCurrentUserReference(
            $this->repository->getUserService()->loadUserByLogin('admin')
        );

        parent::initialize($input, $output); // TODO: Change the autogenerated stub
    }
}

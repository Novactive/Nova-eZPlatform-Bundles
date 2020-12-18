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

namespace Tests\Unit;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Netgen\TagsBundle\API\Repository\Values\Content\Query\Criterion\TagId;
use Netgen\TagsBundle\API\Repository\Values\Content\Query\Criterion\TagKeyword;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\Search;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CriterionTest extends WebTestCase
{
    protected function get(string $serviceId)
    {
        return self::$container->get($serviceId);
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function contentCriterionsProvider(): array
    {
        return [
            [new Criterion\ContentId([57, 58]), '(content_id_i=57 OR content_id_i=58)'],
            [new Criterion\ContentTypeId(2), '(content_type_id_i=2)'],
            [new Criterion\ContentTypeIdentifier('folder'), '(content_type_identifier_s:"folder")'],
            [new Criterion\ContentTypeGroupId(1), '(content_type_group_id_mi=1)'],
            [
                new Criterion\CustomField(
                    'article_author_count_i',
                    Criterion\Operator::EQ,
                    1
                ),
                '(article_author_count_i=1)',
            ],
            [
                new Criterion\DateMetadata(
                    Criterion\DateMetadata::MODIFIED,
                    Criterion\Operator::GT,
                    1598551910
                ),
                'content_modification_date_timestamp_i > 1598551910',
            ],
            [
                new Criterion\DateMetadata(
                    Criterion\DateMetadata::CREATED,
                    Criterion\Operator::BETWEEN,
                    [1598551911, 1598552352]
                ),
                'content_publication_date_timestamp_i:1598551911 TO 1598552352',
            ],
            /*
            [
                new Criterion\IsFieldEmpty('short_title'),
                '(short_title_is_empty_b:true OR short_title_is_empty_b:true)',
            ],
            [
                new Criterion\Field(
                    'title',
                    Criterion\Operator::EQ,
                    'New article 5'
                ),
                '(article_title_value_s:"New article 5" OR form_title_value_s:"New article 5")',
            ],
            [
                new Criterion\FieldRelation(
                    'related_content',
                    Criterion\Operator::IN,
                    [56]
                ),
                '(article_related_content_value_ms:"56")',
            ],
            */
            [
                new Criterion\LanguageCode('fre-FR'),
                '(content_language_codes_ms:"fre-FR" OR content_always_available_b:true)',
            ],
            [
                new Criterion\LogicalNot(
                    new Criterion\LogicalOr(
                        [
                            new Criterion\ContentTypeIdentifier(['folder']),
                            new Criterion\ContentId([56, 58]),
                        ]
                    )
                ),
                '((NOT content_type_identifier_s:"folder") AND (NOT content_id_i=56 AND NOT content_id_i=58))',
            ],
            [
                new Criterion\LogicalAnd(
                    [
                        new Criterion\LogicalNot(
                            new Criterion\ContentTypeIdentifier(['folder'])
                        ),
                        new Criterion\LogicalNot(
                            new Criterion\ContentId([56, 58])
                        ),
                    ]
                ),
                '(NOT content_type_identifier_s:"folder") AND (NOT content_id_i=56 AND NOT content_id_i=58)',
            ],
            [
                new Criterion\LogicalAnd(
                    [
                        new Criterion\ContentTypeIdentifier(['article', 'folder']),
                        new Criterion\LogicalOr(
                            [
                                new Criterion\ParentLocationId(42),
                                new Criterion\ContentTypeId(1),
                            ]
                        ),
                    ]
                ),
                '(content_type_identifier_s:"article" OR content_type_identifier_s:"folder")'.
                ' AND ((location_parent_id_mi=42) OR (content_type_id_i=1))',
            ],
            [new Criterion\MatchAll(), 'content_publication_date_timestamp_i > 0'],
            [new Criterion\MatchNone(), 'content_publication_date_timestamp_i < 0'],
            [new Criterion\ObjectStateId([1, 2]), '(object_state_id_mi=1 OR object_state_id_mi=2)'],
            [
                new Criterion\RemoteId('15aa056813f55caf7f38c7251c1634cc'),
                '(content_remote_id_id:"15aa056813f55caf7f38c7251c1634cc")',
            ],
            [
                new Criterion\SectionId(1),
                '(section_id_i=1)',
            ],
            [
                new Criterion\SectionIdentifier('standard'),
                '(section_identifier_id:"standard")',
            ],
            [
                new Criterion\UserMetadata(Criterion\UserMetadata::GROUP, Criterion\Operator::EQ, 12),
                '(content_owner_user_group_id_mi=12)',
            ],
            [
                new Criterion\Ancestor('/1/2/42/57/'),
                '(location_id_mi=1 OR location_id_mi=2 OR location_id_mi=42 OR location_id_mi=57)',
            ],
            [
                new Criterion\LocationId([57, 58]),
                '(location_id_mi=57 OR location_id_mi=58)',
            ],
            [
                new Criterion\LocationRemoteId('fe716e66498a05e98e6eb9176dee6d36'),
                '(location_remote_id_mid:"fe716e66498a05e98e6eb9176dee6d36")',
            ],
            [
                new Criterion\ParentLocationId([42, 57]),
                '(location_parent_id_mi=42 OR location_parent_id_mi=57)',
            ],
            [
                new Criterion\Visibility(Criterion\Visibility::VISIBLE),
                'location_visible_b:true',
            ],
            /*
            [
                new TagId([1, 2]),
                '(custom_type_eztags_tag_ids_mi=1 OR custom_type_eztags_tag_ids_mi=2)',
            ],
            [
                new TagKeyword(Criterion\Operator::IN, ['specific', 'random']),
                '(custom_type_eztags_tag_keywords_ms:"specific" OR custom_type_eztags_tag_keywords_ms:"random")',
            ],
            */
        ];
    }

    public function locationCriterionsProvider(): array
    {
        return [
            [
                new Criterion\Ancestor('/1/2/42/57/'),
                '(location_id_i=1 OR location_id_i=2 OR location_id_i=42 OR location_id_i=57)',
            ],
            [
                new Criterion\Location\Depth(Criterion\Operator::GT, 2),
                'depth_i > 2',
            ],
            [
                new Criterion\Location\IsMainLocation(Criterion\Location\IsMainLocation::MAIN),
                'is_main_location_b:true',
            ],
            [
                new Criterion\LocationId(58),
                '(location_id_i=58)',
            ],
            [
                new Criterion\LocationRemoteId(
                    ['d67f2ad3aec8ffde1902ca5024f4d3cb', 'cd7db7b26eb25fe77f71f09ab9387148']
                ),
                '(location_remote_id_id:"d67f2ad3aec8ffde1902ca5024f4d3cb" OR '.
                'location_remote_id_id:"cd7db7b26eb25fe77f71f09ab9387148")',
            ],
            [
                new Criterion\ParentLocationId(42),
                '(parent_id_i=42)',
            ],
            [
                new Criterion\Location\Priority(Criterion\Operator::BETWEEN, [0, 2]),
                'priority_i:0 TO 2',
            ],
            [
                new Criterion\Visibility(Criterion\Visibility::VISIBLE),
                'invisible_b:false',
            ],
        ];
    }

    /**
     * @dataProvider contentCriterionsProvider
     */
    public function testContentCriterions(Criterion $criterion, string $expectedValue): void
    {
        /** @var Search $searchService */
        $searchService = $this->get('ezplatform.search.algolia.query.content.search');

        $query = new Query();
        $query->filter = $criterion;

        self::assertEquals($expectedValue, $searchService->visitFilter($criterion));
    }

    /**
     * @dataProvider locationCriterionsProvider
     */
    public function testLocationCriterions(Criterion $criterion, string $expectedValue): void
    {
        /** @var Search $searchService */
        $searchService = $this->get('ezplatform.search.algolia.query.location.search');

        $query = new LocationQuery();
        $query->filter = $criterion;

        self::assertEquals($expectedValue, $searchService->visitFilter($criterion));
    }
}

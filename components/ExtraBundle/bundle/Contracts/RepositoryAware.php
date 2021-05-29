<?php

/**
 * NovaeZExtraBundle RepositoryAware.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZExtraBundle\Contracts;

use eZ\Publish\API\Repository\Repository as ApiRepository;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\RelationList;
use eZ\Publish\API\Repository\Values\Content\RelationList\Item\RelationListItem;

trait RepositoryAware
{
    protected ApiRepository $repository;

    /**
     * @required
     */
    public function setRepository(ApiRepository $repository): void
    {
        $this->repository = $repository;
    }

    public function loadReverseRelations(
        ContentInfo $contentInfo,
        int $offset = 0,
        int $limit = -1
    ): RelationList {
        $reverseRelations = $this->repository->sudo(
            fn (ApiRepository $repo) => $repo->getContentService()->loadReverseRelationList(
                $contentInfo,
                $offset,
                $limit
            )
        );

        $filtered = [];
        /** @var RelationListItem $reverseRelation */
        foreach ($reverseRelations as $reverseRelation) {
            if (!$reverseRelation->hasRelation()) {
                continue;
            }

            $filtered[] = $reverseRelation;
        }

        return new RelationList(['items' => $filtered]);
    }
}

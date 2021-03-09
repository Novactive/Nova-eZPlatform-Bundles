<?php

/**
 * NovaeZSlackBundle Bundle.
 *
 * @package   Novactive\Bundle\eZSlackBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZSlackBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZSlackBundle\Repository;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Query as eZQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

class Trash
{
    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function checkIfContentIsInTrash(Content $content): bool
    {
        $query = new eZQuery();
        $query->filter = new Criterion\ContentTypeId($content->contentInfo->contentTypeId);
        $result = $this->repository->getTrashService()->findTrashItems($query);
        foreach ($result->items as $item) {
            if ($item->contentInfo->id === $content->id) {
                return true;
            }
        }

        return false;
    }
}

<?php

/**
 * NovaeZExtraBundle ContentExtension.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZExtraBundle\Twig;

use Exception;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\FieldType\Relation\Value as RelationValue;
use eZ\Publish\Core\FieldType\RelationList\Value as RelationListValue;
use eZ\Publish\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension as KernelContentExtension;
use Twig\TwigFunction;

class ContentExtension extends KernelContentExtension
{
    public function getFunctions(): array
    {
        return array_merge(
            parent::getFunctions(),
            [
                new TwigFunction('eznova_parentcontent_by_contentinfo', [$this, 'parentContentByContentInfo']),
                new TwigFunction('eznova_location_by_content', [$this, 'locationByContent']),
                new TwigFunction('eznova_relation_field_to_content', [$this, 'relationFieldToContent']),
                new TwigFunction(
                    'eznova_relationlist_field_to_content_list',
                    [$this, 'relationsListFieldToContentList']
                ),
            ]
        );
    }

    public function parentContentByContentInfo(ContentInfo $contentInfo): Content
    {
        $repository = $this->repository;
        $location = $repository->getLocationService()->loadLocation($contentInfo->mainLocationId);
        $parentLocation = $repository->getLocationService()->loadLocation($location->parentLocationId);

        return $parentLocation->getContent();
    }

    public function locationByContent(Content $content): Location
    {
        return $this->repository->getLocationService()->loadLocation($content->contentInfo->mainLocationId);
    }

    /**
     * @return Content|false
     */
    public function relationFieldToContent(RelationValue $fieldValue)
    {
        try {
            $content = $this->repository->getContentService()->loadContent($fieldValue->destinationContentId);
            $location = $this->repository->getLocationService()->loadLocation($content->contentInfo->mainLocationId);

            if ((1 === $location->invisible) || (1 === $location->hidden)) {
                return false;
            }
        } catch (NotFoundException $e) {
            return false;
        }

        return $content;
    }

    public function relationsListFieldToContentList(RelationListValue $fieldValue): array
    {
        $repository = $this->repository;
        $list = [];
        foreach ($fieldValue->destinationContentIds as $id) {
            try {
                $content = $repository->getContentService()->loadContent($id);
                $location = $repository->getLocationService()->loadLocation($content->contentInfo->mainLocationId);

                if (1 == $location->invisible or 1 == $location->hidden) {
                    continue;
                }
                $list[] = $content;
            } catch (Exception $exception) {
                return [];
            }
        }

        return $list;
    }
}

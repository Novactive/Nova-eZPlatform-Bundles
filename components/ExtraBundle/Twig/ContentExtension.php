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

namespace Novactive\Bundle\eZExtraBundle\Twig;

use Exception;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\FieldType\Relation\Value as RelationValue;
use eZ\Publish\Core\FieldType\RelationList\Value as RelationListValue;
use eZ\Publish\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension as KernelContentExtension;
use Twig_Function_Method;

/**
 * Class ContentExtension.
 */
class ContentExtension extends KernelContentExtension
{
    /**
     * Functions of our Extension.
     *
     * @return array
     */
    public function getFunctions()
    {
        return array_merge(
            parent::getFunctions(),
            [
                'eznova_parentcontent_by_contentinfo' => new Twig_Function_Method(
                    $this,
                    'parentContentByContentInfo'
                ),
                'eznova_location_by_content' => new Twig_Function_Method(
                    $this,
                    'locationByContent'
                ),
                'eznova_relation_field_to_content' => new Twig_Function_Method(
                    $this,
                    'relationFieldToContent'
                ),
                'eznova_relationlist_field_to_content_list' => new Twig_Function_Method(
                    $this,
                    'relationsListFieldToContentList'
                ),
            ]
        );
    }

    /**
     * ParentContentByContentInfo.
     *
     * @return Content
     */
    public function parentContentByContentInfo(ContentInfo $contentInfo)
    {
        $repository = $this->repository;
        $location = $repository->getLocationService()->loadLocation($contentInfo->mainLocationId);
        $parentLocation = $repository->getLocationService()->loadLocation($location->parentLocationId);

        return $this->contentByContentInfo($parentLocation->contentInfo);
    }

    /**
     * LocationByContent.
     */
    public function locationByContent(Content $content)
    {
        return $this->repository->getLocationService()->loadLocation($content->contentInfo->mainLocationId);
    }

    /**
     * RelationFieldToContent.
     *
     * @return Content|false
     */
    public function relationFieldToContent(RelationValue $fieldValue)
    {
        try {
            $content = $this->repository->getContentService()->loadContent($fieldValue->destinationContentId);
            $location = $this->repository->getLocationService()->loadLocation($content->contentInfo->mainLocationId);

            if ((1 == $location->invisible) || (1 == $location->hidden)) {
                return false;
            }
        } catch (NotFoundException $e) {
            return false;
        }

        return $content;
    }

    /**
     * RelationsListFieldToContentList.
     *
     * @return array
     */
    public function relationsListFieldToContentList(RelationListValue $fieldValue)
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
            } catch (Exception $ex) {
                return [];
            }
        }

        return $list;
    }
}

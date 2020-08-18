<?php
/**
 * NovaeZExtraBundle ContentExtension
 *
 * @package   Novactive\Bundle\eZExtraBundle
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */
namespace Novactive\Bundle\eZExtraBundle\Twig;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension as KernelContentExtension;
use eZ\Publish\Core\FieldType\Relation\Value as RelationValue;
use eZ\Publish\Core\FieldType\RelationList\Value as RelationListValue;
use Twig_Extension;
use Twig_Function_Method;

/**
 * Class ContentExtension
 */
class ContentExtension extends KernelContentExtension
{

    /**
     * Functions of our Extension
     *
     * @return array
     */
    public function getFunctions()
    {
        return array_merge(
            parent::getFunctions(),
            array (
                'eznova_content_by_contentinfo'             => new Twig_Function_Method(
                    $this, 'contentByContentInfo'
                ),
                'eznova_parentcontent_by_contentinfo'       => new Twig_Function_Method(
                    $this, 'parentContentByContentInfo'
                ),
                'eznova_contenttype_by_content'             => new Twig_Function_Method(
                    $this, 'contentTypeByContent'
                ),
                'eznova_location_by_content'                => new Twig_Function_Method(
                    $this, 'locationByContent'
                ),
                'eznova_relation_field_to_content'          => new Twig_Function_Method(
                    $this, 'relationFieldToContent'
                ),
                'eznova_relationlist_field_to_content_list' => new Twig_Function_Method(
                    $this, 'relationsListFieldToContentList'
                )
            )
        );
    }

    /**
     * ContentByContentInfo
     *
     * @param ContentInfo $contentInfo
     *
     * @return Content
     */
    public function contentByContentInfo(ContentInfo $contentInfo)
    {
        return $this->repository->getContentService()->loadContentByContentInfo($contentInfo);
    }

    /**
     * ParentContentByContentInfo
     *
     * @param ContentInfo $contentInfo
     *
     * @return Content
     */
    public function parentContentByContentInfo(ContentInfo $contentInfo)
    {
        $repository     = $this->repository;
        $location       = $repository->getLocationService()->loadLocation($contentInfo->mainLocationId);
        $parentLocation = $repository->getLocationService()->loadLocation($location->parentLocationId);

        return $this->contentByContentInfo($parentLocation->contentInfo);
    }

    /**
     * ContentTypeByContent
     *
     * @param Content $content
     *
     * @return ContentType
     */
    public function contentTypeByContent(Content $content)
    {
        return $this->repository->getContentTypeService()->loadContentType($content->contentInfo->contentTypeId);
    }

    /**
     * LocationByContent
     *
     * @param Content $content
     *
     * @return mixed
     */
    public function locationByContent(Content $content)
    {
        return $this->repository->getLocationService()->loadLocation($content->contentInfo->mainLocationId);
    }

    /**
     * RelationFieldToContent
     *
     * @param RelationValue $fieldValue
     *
     * @return Content|false
     */
    public function relationFieldToContent(RelationValue $fieldValue)
    {
        try {

            $content  = $this->repository->getContentService()->loadContent($fieldValue->destinationContentId);
            $location = $this->repository->getLocationService()->loadLocation($content->contentInfo->mainLocationId);

            if (($location->invisible == 1) || ($location->hidden == 1)) {
                return false;
            }
        } catch (\eZ\Publish\API\Repository\Exceptions\NotFoundException $e) {
            return false;
        }

        return $content;
    }

    /**
     * RelationsListFieldToContentList
     *
     * @param RelationListValue $fieldValue
     *
     * @return array
     */
    public function relationsListFieldToContentList(RelationListValue $fieldValue)
    {
        $repository = $this->repository;
        $list       = array ();
        foreach ($fieldValue->destinationContentIds as $id) {
            try {
                $content  = $repository->getContentService()->loadContent($id);
                $location = $repository->getLocationService()->loadLocation($content->contentInfo->mainLocationId);

                if ($location->invisible == 1 or $location->hidden == 1) {
                    continue;
                }
                $list[] = $content;
            } catch (\Exception $ex) {
                //return empty
            }
        }

        return $list;
    }
}

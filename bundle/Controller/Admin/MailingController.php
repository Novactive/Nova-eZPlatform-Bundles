<?php
/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Controller\Admin;

use eZ\Publish\API\Repository\Repository;
use EzSystems\EzPlatformAdminUi\Tab\LocationView\ContentTab;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class MailingController.
 *
 * @Route("/mailing")
 */
class MailingController
{
    /**
     * @Route("/show/{mailing}", name="novaezmailing_mailing_show")
     * @Template()
     *
     * @return array
     */
    public function showAction(Mailing $mailing): array
    {
        return [
            'item' => $mailing,
        ];
    }

    /**
     * @Template()
     *
     * @return array
     */
    public function mailingTabsAction(Mailing $mailing, Repository $repository, ContentTab $contentTab): array
    {
        $content     = $mailing->getContent();
        $location    = $repository->getLocationService()->loadLocation(
            $content->contentInfo->mainLocationId
        );
        $contentType = $repository->getContentTypeService()->loadContentType(
            $content->contentInfo->contentTypeId
        );
        $preview     = $contentTab->renderView(
            [
                'content'     => $content,
                'location'    => $location,
                'contentType' => $contentType,
            ]
        );

        return [
            'item'    => $mailing,
            'preview' => $preview,
        ];
    }
}

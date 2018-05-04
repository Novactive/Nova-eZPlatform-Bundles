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
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use EzSystems\EzPlatformAdminUi\Tab\LocationView\ContentTab;
use EzSystems\EzPlatformAdminUi\UI\Module\Subitems\ContentViewParameterSupplier;
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
    public function showAction(Mailing $mailing, ContentViewParameterSupplier $contentViewParameterSupplier): array
    {
        $contentView = new ContentView();
        $contentView->setLocation($mailing->getLocation());
        $contentViewParameterSupplier->supply($contentView);

        return [
            'item'            => $mailing,
            'subitems_module' => $contentView->getParameter('subitems_module'),
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
        $contentType = $repository->getContentTypeService()->loadContentType(
            $content->contentInfo->contentTypeId
        );
        $preview     = $contentTab->renderView(
            [
                'content'     => $content,
                'location'    => $mailing->getLocation(),
                'contentType' => $contentType,
            ]
        );

        return [
            'item'    => $mailing,
            'preview' => $preview,
        ];
    }

    /**
     * @Route("/edit/{mailing}", name="novaezmailing_mailing_edit")
     * @Template()
     *
     * @return array
     */
    public function editAction(Mailing $mailing): array
    {
        return [
            'item' => $mailing,
        ];
    }
}

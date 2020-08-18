<?php

/**
 * NovaeZExtraBundle PictureController.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

namespace Novactive\Bundle\eZExtraBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PictureController.
 */
class PictureController extends Controller
{
    /**
     * Controller to handler image alias of an content id.
     *
     * @param int    $contentId
     * @param string $fieldIdentifier
     * @param string $alias
     * @param array  $options
     *
     * @return array|Response
     * @Template
     */
    public function aliasAction($contentId, $fieldIdentifier, $alias, $options = [])
    {
        $repository = $this->getRepository();
        try {
            $contentService = $repository->getContentService();
            $content = $contentService->loadContent($contentId);

            return [
                'picture' => $content, 'fieldIdentifier' => $fieldIdentifier, 'alias' => $alias, 'options' => $options,
            ];
        } catch (\Exception $e) {
            $r = new Response();
            $r->setContent("Object $contentId doesn't exist ($fieldIdentifier, $alias)");

            return $r;
        }
    }
}

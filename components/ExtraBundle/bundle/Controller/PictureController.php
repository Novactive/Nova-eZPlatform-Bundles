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

declare(strict_types=1);

namespace Novactive\Bundle\eZExtraBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class PictureController extends Controller
{
    /**
     * Controller to handler image alias of an content id.
     *
     * @return array|Response
     * @Template
     */
    public function aliasAction(int $contentId, string $fieldIdentifier, string $alias, array $options = [])
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

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

use Exception;
use Ibexa\Bundle\Core\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PictureController extends Controller
{
    #[Route('/picture/{contentId}/{fieldIdentifier}/{alias}', name: 'novaezextra_picture_alias', methods: ['GET'])]
    public function aliasAction(int $contentId, string $fieldIdentifier, string $alias, array $options = []): Response
    {
        $repository = $this->getRepository();
        try {
            $contentService = $repository->getContentService();
            $content = $contentService->loadContent($contentId);

            return $this->render('@NovaeZExtraBundle/Picture/alias.html.twig', [
                'picture' => $content,
                'fieldIdentifier' => $fieldIdentifier,
                'alias' => $alias,
                'options' => $options,
            ]);
        } catch (Exception) {
            $r = new Response();
            $r->setContent("Object $contentId doesn't exist ($fieldIdentifier, $alias)");

            return $r;
        }
    }
}

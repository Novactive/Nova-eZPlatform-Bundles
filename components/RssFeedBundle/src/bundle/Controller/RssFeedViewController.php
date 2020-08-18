<?php
/**
 * NovaeZRssFeedBundle.
 *
 * @package   NovaeZRssFeedBundle
 *
 * @author    Novactive
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZRssFeedBundle/blob/master/LICENSE
 */

namespace Novactive\EzRssFeedBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\Core\Base\Exceptions\UnauthorizedException;
use Novactive\EzRssFeedBundle\Entity\RssFeeds;
use Novactive\EzRssFeedBundle\Services\RssFeedsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/rss/feed")
 *
 * Class RssFeedController
 *
 * @package Novactive\EzRssFeedBundle\Controller
 */
class RssFeedViewController extends Controller
{
    use EntityManagerTrait;
    /**
     * @Route("/{urlSlug}", name="rss_feed_view_index")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request): Response
    {
        /**
         * @var PermissionResolver
         */
        $permissionResolver = $this->container->get('ezpublish.api.repository')->getPermissionResolver();

        if (!$permissionResolver->hasAccess('rss', 'read')) {
            throw new UnauthorizedException(
                'rss',
                'read',
                []
            );
        }

        $rssFeedRepository = $this->entityManager->getRepository(RssFeeds::class);

        $rssFeed = $rssFeedRepository->findOneBy(
            [
                'urlSlug' => $request->get('urlSlug'),
                'status'  => RssFeeds::STATUS_ENABLED,
            ]
        );

        if ($rssFeed) {
            /**
             * @var RssFeedsService
             */
            $rssFeedsService = $this->get('Novactive\EzRssFeedBundle\Services\RssFeedsService');

            $feedItems = $rssFeedsService->fetchContent($rssFeed);

            $response = new Response(
                $this->renderView(
                    '@ezdesign/rssfeed/view.html.twig',
                    [
                        'rssFeed' => [
                            'meta' => [
                                'title'       => $rssFeed->getTitle(),
                                'link'        => $request->getUri(),
                                'description' => $rssFeed->getDescription(),
                            ],
                            'feedItems' => $feedItems,
                        ],
                    ]
                )
            );

            $response->headers->set('Content-Type', 'application/rss+xml; charset=utf-8');

            return $response;
        } else {
            throw $this->createNotFoundException();
        }
    }
}

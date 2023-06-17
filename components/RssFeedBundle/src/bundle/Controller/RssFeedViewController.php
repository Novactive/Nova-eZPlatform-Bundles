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

use Ibexa\Bundle\Core\Controller;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Knp\Menu\FactoryInterface;
use Novactive\EzRssFeedBundle\Entity\RssFeeds;
use Novactive\EzRssFeedBundle\Services\RssFeedsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/rss/feed")
 *
 * @package Novactive\EzRssFeedBundle\Controller
 */
class RssFeedViewController extends Controller
{
    use EntityManagerTrait;

    /**
     * @Route("/{site}/{urlSlug}", name="rss_feed_view_index")
     */
    public function indexAction(Request $request, RssFeedsService $rssFeedsService): Response
    {
        /**
         * @var PermissionResolver $permissionResolver
         */
        $permissionResolver = $this->container->get('ibexa.api.repository')->getPermissionResolver();

        if (!$permissionResolver->hasAccess('rss', 'read')) {
            throw new UnauthorizedException('rss', 'read', []);
        }

        $rssFeedRepository = $this->entityManager->getRepository(RssFeeds::class);

        $rssFeed = $rssFeedRepository->findFeedBySiteIdentifierAndUrlSlug(
            $request->get('site'),
            $request->get('urlSlug')
        );

        if ($rssFeed instanceof RssFeeds) {
            $feedItems = $rssFeedsService->fetchContent($rssFeed);
            $response = new Response(
                $this->renderView(
                    '@ibexadesign/rssfeed/view.html.twig',
                    [
                        'rssFeed' => [
                            'meta' => [
                                'title' => $rssFeed->getTitle(),
                                'link' => $request->getUri(),
                                'description' => $rssFeed->getDescription(),
                            ],
                            'feedItems' => $feedItems,
                        ],
                    ]
                )
            );

            $response->headers->set('Content-Type', 'application/rss+xml; charset=utf-8');

            return $response;
        }

        throw $this->createNotFoundException();
    }

    public function rssHeadLinkTagsAction(SiteAccess $siteAccess, FactoryInterface $knpMenuFactory): Response
    {
        $rssFeedRepository = $this->entityManager->getRepository(RssFeeds::class);
        /** @var RssFeeds[] $rssFeeds */
        $rssFeeds = $rssFeedRepository->findFeedsBySiteIdentifier(
            $siteAccess->name
        );
        $links = [];
        foreach ($rssFeeds as $rssFeed) {
            if (empty($links) || 0 !== $rssFeed->getFeedSites()->count()) {
                $links[] = $knpMenuFactory->createItem(
                    $rssFeed->getTitle(),
                    [
                        'route' => 'rss_feed_view_index',
                        'routeParameters' => [
                            'site' => $siteAccess->name,
                            'urlSlug' => $rssFeed->getUrlSlug(),
                        ],
                        'routeAbsolute' => true,
                    ]
                );
            }
        }

        return $this->render(
            '@ibexadesign/rssfeed/meta_links.html.twig',
            [
                'links' => $links,
            ]
        );
    }
}

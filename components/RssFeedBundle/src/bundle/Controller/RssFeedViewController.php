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

use DateTime;
use FOS\HttpCacheBundle\Http\SymfonyResponseTagger;
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

    protected SymfonyResponseTagger $responseTagger;
    protected ?int $cacheTtl = null;

    public function __construct(SymfonyResponseTagger $responseTagger, ?int $cacheTtl)
    {
        $this->responseTagger = $responseTagger;
        $this->cacheTtl = $cacheTtl;
    }

    /**
     * @Route("/{urlSlug}/{site?}", name="rss_feed_view_index")
     */
    public function indexAction(
        Request $request,
        SiteAccess $siteAccess,
        RssFeedsService $rssFeedsService
    ): Response {
        /**
         * @var PermissionResolver $permissionResolver
         */
        $permissionResolver = $this->container->get('ibexa.api.repository')->getPermissionResolver();

        if (!$permissionResolver->hasAccess('rss', 'read')) {
            throw new UnauthorizedException('rss', 'read', []);
        }

        $rssFeedRepository = $this->entityManager->getRepository(RssFeeds::class);

        $rssFeed = $rssFeedRepository->findFeedBySiteIdentifierAndUrlSlug(
            $request->query->get('site', $siteAccess->name),
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

            $response->setPublic();
            if (null === $this->cacheTtl) {
                $expire = (new DateTime())->modify('+1 day')->setTime(0, 0);
                $response->setExpires($expire);
                $response->setSharedMaxAge($expire->getTimestamp() - time());
            } elseif (0 === $this->cacheTtl) {
                $response->setPrivate();
            } else {
                $response->setSharedMaxAge($this->cacheTtl);
            }
            $this->responseTagger->addTags(['rssfeed-'.$rssFeed->getId()]);
            $this->responseTagger->tagSymfonyResponse($response);

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
                            'urlSlug' => $rssFeed->getUrlSlug(),
                        ],
                        'routeAbsolute' => true,
                    ]
                );
            }
        }

        $response = new Response();
        $response = $this->render(
            '@ibexadesign/rssfeed/meta_links.html.twig',
            [
                'links' => $links,
            ],
            $response
        );

        $response->setPublic();
        $response->setSharedMaxAge(60 * 60 * 24 * 365);
        $this->responseTagger->addTags(['rssfeeds']);
        $this->responseTagger->tagSymfonyResponse($response);

        return $response;
    }
}

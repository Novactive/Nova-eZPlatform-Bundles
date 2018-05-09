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

namespace Novactive\Bundle\eZMailingBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Novactive\Bundle\eZMailingBundle\Core\Utils\Browser;
use Novactive\Bundle\eZMailingBundle\Entity\StatHit;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TrackController.
 *
 * @Route("/t")
 */
class TrackController
{
    const PIXEL_CONTENT      = 'R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==';
    const PIXEL_CONTENT_TYPE = 'image/gif';

    /**
     * @Route("/continue/{salt}/{broadcastId}/{url}", name="novaezmailing_t_continue")
     *
     * @param string                 $salt
     * @param int                    $broadcastId
     * @param string                 $url
     * @param EntityManagerInterface $entityManager
     * @param Request                $request
     *
     * @return RedirectResponse
     */
    public function continueAction(
        string $salt,
        int $broadcastId,
        string $url,
        EntityManagerInterface $entityManager,
        Request $request
    ): RedirectResponse {
        $broadcast  = $entityManager->getRepository('NovaeZMailingBundle:Broadcast')->findOneByid($broadcastId);
        $browser    = new Browser($request->headers->get('User-Agent'));
        $stat       = new StatHit();
        $decodedUrl = base64_decode($url);
        $stat->setOsName($browser->getPlatform());
        $stat->setBrowserName($browser->getName());
        $stat->setUserKey($salt);
        $stat->setUrl($decodedUrl);
        $stat->setBroadcast($broadcast);
        $entityManager->persist($stat);
        $entityManager->flush();

        return new RedirectResponse($decodedUrl);
    }

    /**
     * @Route("/read/{salt}/{broadcastId}", name="novaezmailing_t_read")
     *
     * @param string                 $salt
     * @param int                    $broadcastId
     * @param EntityManagerInterface $entityManager
     * @param Request                $request
     *
     * @return Response
     */
    public function readAction(
        string $salt,
        int $broadcastId,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        $broadcast = $entityManager->getRepository('NovaeZMailingBundle:Broadcast')->findOneByid($broadcastId);
        $browser   = new Browser($request->headers->get('User-Agent'));
        $stat      = new StatHit();
        $stat->setOsName($browser->getPlatform());
        $stat->setBrowserName($browser->getName());
        $stat->setUserKey($salt);
        $stat->setUrl('-');
        $stat->setBroadcast($broadcast);
        $entityManager->persist($stat);
        $entityManager->flush();

        $response = new Response(base64_decode(self::PIXEL_CONTENT));
        $response->headers->set('Content-Type', self::PIXEL_CONTENT_TYPE);
        $response->setPrivate();
        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }
}

<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\CaptchEtatBundle\Controller;

use AlmaviaCX\Bundle\CaptchEtat\Api\Gateway;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class CaptchEtatController
{
    public function __construct(
        Gateway $gateway
    ) {
        $this->gateway = $gateway;
    }

    /**
     * Permet au captcha (programme js/html) d'appeler l'api en passant par le serveur.
     */
    public function apiSimpleCaptchaEndpointAction(Request $request): Response
    {
        $get = $request->get('get');
        $tech = $request->get('t');
        $type = $request->get('c');
        $content = $this->gateway->getSimpleCaptchaEndpoint($get, $tech, $type);
        $response = new Response($content);

        if ('sound' === $get) {
            $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE,
                'captcha-sound.wave'
            ));
            $response->headers->set('Content-Type', 'audio/x-wav');
        } else {
            $response->headers->set('Content-Type', 'application/json');
        }
        $response->setPrivate();

        return $response;
    }
}

<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\CaptchEtatBundle\Controller;

use AlmaviaCX\Bundle\CaptchEtat\Api\Gateway;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

readonly class CaptchEtatController
{
    public function __construct(
        protected Gateway $gateway
    ) {
    }

    /**
     * Permet au captcha (programme js/html) d'appeler l'api en passant par le serveur.
     */
    public function apiSimpleCaptchaEndpointAction(Request $request): Response
    {
        $get = (string) $this->get($request, 'get');
        $tech = (string) $this->get($request, 't');
        $type = (string) $this->get($request, 'c');
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

    protected function get(Request $request, string $key): ?string
    {
        if ($request->attributes->get($key)) {
            return $request->attributes->get($key);
        }

        if ($request->query->has($key)) {
            return $request->query->all()[$key];
        }

        if ($request->request->has($key)) {
            return $request->request->all()[$key];
        }

        return null;
    }
}

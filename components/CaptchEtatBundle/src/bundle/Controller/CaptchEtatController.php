<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\CaptchEtatBundle\Controller;

use AlmaviaCX\Bundle\CaptchEtat\Api\Gateway;
use AlmaviaCX\Bundle\CaptchEtat\Challenge\ChallengeGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class CaptchEtatController
{
    protected Gateway $gateway;
    protected ChallengeGenerator $challengeGenerator;

    public function __construct(
        Gateway $gateway,
        ChallengeGenerator $challengeGenerator
    ) {
        $this->gateway = $gateway;
        $this->challengeGenerator = $challengeGenerator;
    }

    /**
     * Permet au captcha (programme js/html) d'appeler l'api en passant par le serveur.
     */
    public function apiSimpleCaptchaEndpointAction(Request $request): Response
    {
        $get = $request->get('get');
        $tech = $request->get('t');
        $type = $request->get('c');
        $content = $this->gateway->getSimpleCaptchaEndpoint($get, null, $tech, $type);
        $response = new Response($content);
        if ('script-include' === $get) {
            $response->headers->set('Content-Type', 'text/javascript');
        } elseif ('image' === $get) {
            $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE,
                'captcha.png'
            ));
            $response->headers->set('Content-Type', 'image/png');
        } elseif ('sound' === $get) {
            $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE,
                'captcha-sound.wave'
            ));
            $response->headers->set('Content-Type', 'audio/wave');
        }
        $response->setPrivate();

        return $response;
    }

    public function getCaptcha(): Response
    {
        $challenge = ($this->challengeGenerator)();
        $response = new Response($challenge->captchaHtml);
        $response->setPrivate();

        return $response;
    }
}

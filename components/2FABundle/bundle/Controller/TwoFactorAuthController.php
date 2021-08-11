<?php

/*
 * NovaeZ2FABundle.
 *
 * @package   NovaeZ2FABundle
 *
 * @author    Maxim Strukov <maxim.strukov@almaviacx.com>
 * @copyright 2021 AlmaviaCX
 * @license   https://github.com/Novactive/NovaeZ2FA/blob/main/LICENSE
 */

namespace Novactive\Bundle\eZ2FABundle\Controller;

use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Novactive\Bundle\eZ2FABundle\Core\QRCodeGenerator;
use Novactive\Bundle\eZ2FABundle\Entity\UserGoogleAuthSecret;
use Novactive\Bundle\eZ2FABundle\Form\Type\TwoFactorAuthType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticator;
use Novactive\Bundle\eZ2FABundle\Core\SiteAccessAwareQueryExecutor;

class TwoFactorAuthController extends Controller
{
    public function setupAction(
        Request $request,
        GoogleAuthenticator $googleAuthenticator,
        SiteAccessAwareQueryExecutor $queryExecutor,
        QRCodeGenerator $QRCodeGenerator
    ): Response {
        /* @var UserGoogleAuthSecret $user */
        $user = $this->getUser();

        if ($user->isSetupComplete()) {
            return $this->render(
                '@ezdesign/2fa/setup.html.twig',
                [
                    'reset' => true
                ]
            );
        }

        $secretKey = $user->getGoogleAuthenticatorSecret();
        if (null === $secretKey) {
            $secretKey = $googleAuthenticator->generateSecret();
            $user->setGoogleAuthenticatorSecret($secretKey);
        }

        $form = $this->createForm(TwoFactorAuthType::class, ['secretKey' => $secretKey]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($googleAuthenticator->checkCode($user, $data['sixdigitCode'])) {
                $queryExecutor->insertUserGoogleAuthSecret($user->getAPIUser()->id, $data['secretKey']);
                $user->setupComplete();

                return $this->render(
                    '@ezdesign/2fa/setup.html.twig',
                    [
                        'success' => true
                    ]
                );
            }

            $form->get('sixdigitCode')->addError(new FormError('Wrong 6-digit code provided!'));
        }

        return $this->render(
            '@ezdesign/2fa/setup.html.twig',
            [
                'qrCode' => $QRCodeGenerator->createFromUser($user),
                'form' => $form->createView()
            ]
        );
    }

    public function resetAction(SiteAccessAwareQueryExecutor $queryExecutor): RedirectResponse
    {
        /* @var UserGoogleAuthSecret $user */
        $user = $this->getUser();

        $queryExecutor->deleteUserGoogleAuthSecret($user->getAPIUser()->id);
        $user->setupComplete(false);

        return $this->redirectToRoute('2fa_setup');
    }
}
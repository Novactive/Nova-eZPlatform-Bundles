<?php

/**
 * NovaeZ2FABundle.
 *
 * @package   NovaeZ2FABundle
 *
 * @author    Maxim Strukov <maxim.strukov@almaviacx.com>
 * @copyright 2021 AlmaviaCX
 * @license   https://github.com/Novactive/NovaeZ2FA/blob/main/LICENSE
 */

namespace Novactive\Bundle\eZ2FABundle\Controller;

use eZ\Publish\Core\MVC\Symfony\Security\User;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Novactive\Bundle\eZ2FABundle\Core\QRCodeGenerator;
use Novactive\Bundle\eZ2FABundle\Core\UserRepository;
use Novactive\Bundle\eZ2FABundle\Entity\UserGoogleAuthSecret;
use Novactive\Bundle\eZ2FABundle\Form\Type\TwoFactorAuthType;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticator;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuthController extends Controller
{
    public function setupAction(
        Request $request,
        GoogleAuthenticator $googleAuthenticator,
        UserRepository $userRepository,
        QRCodeGenerator $QRCodeGenerator
    ): Response {
        /* @var User $user */
        $user = $this->getUser();

        if ($userRepository->getUserGoogleAuthSecretByUserId($user->getAPIUser()->id)) {
            return $this->render(
                '@ezdesign/2fa/setup.html.twig',
                [
                    'reset' => true,
                ]
            );
        }

        $user = new UserGoogleAuthSecret($user->getAPIUser(), $user->getRoles(), null);

        $form = $this->createForm(TwoFactorAuthType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user->setGoogleAuthenticatorSecret($data['secretKey']);
            if ($googleAuthenticator->checkCode($user, $data['sixdigitCode'])) {
                $userRepository->insertUserGoogleAuthSecret($user->getAPIUser()->id, $data['secretKey']);

                return $this->render(
                    '@ezdesign/2fa/setup.html.twig',
                    [
                        'success' => true,
                    ]
                );
            }

            $form->get('sixdigitCode')->addError(new FormError('Wrong 6-digit code provided!'));
        }

        if (!$form->isSubmitted()) {
            $secretKey = $googleAuthenticator->generateSecret();
            $user->setGoogleAuthenticatorSecret($secretKey);
            $form->get('secretKey')->setData($secretKey);
        }

        return $this->render(
            '@ezdesign/2fa/setup.html.twig',
            [
                'qrCode' => $QRCodeGenerator->createFromUser($user),
                'form' => $form->createView(),
            ]
        );
    }

    public function resetAction(UserRepository $userRepository): RedirectResponse
    {
        /* @var UserGoogleAuthSecret $user */
        $user = $this->getUser();

        $userRepository->deleteUserGoogleAuthSecret($user->getAPIUser()->id);

        return $this->redirectToRoute('2fa_setup');
    }
}

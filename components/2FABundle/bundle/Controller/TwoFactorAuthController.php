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
use Novactive\Bundle\eZ2FABundle\Entity\UserTotpAuthSecret;
use Novactive\Bundle\eZ2FABundle\Form\Type\TwoFactorAuthType;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticator;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticator;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuthController extends Controller
{
    public function setupAction(
        Request $request,
        GoogleAuthenticator $googleAuthenticator,
        TotpAuthenticator $totpAuthenticator,
        UserRepository $userRepository,
        QRCodeGenerator $QRCodeGenerator
    ): Response {
        /* @var User $user */
        $user = $this->getUser();

        $userAuthSecrets = $userRepository->getUserAuthSecretByUserId($user->getAPIUser()->id);

        if (
            is_array($userAuthSecrets) &&
            (
                !empty($userAuthSecrets['google_authentication_secret']) ||
                !empty($userAuthSecrets['totp_authentication_secret'])
            )
        ) {
            return $this->render(
                '@ezdesign/2fa/setup.html.twig',
                [
                    'reset' => true,
                ]
            );
        }

        $user = new UserTotpAuthSecret($user->getAPIUser(), $user->getRoles());

        $form = $this->createForm(TwoFactorAuthType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user->setAuthenticatorSecret($data['secretKey']);
            if ($totpAuthenticator->checkCode($user, $data['sixdigitCode'])) {
                if (is_array($userAuthSecrets)) {
                    $userRepository->updateUserTotpAuthSecret($user->getAPIUser()->id, $data['secretKey']);
                } else {
                    $userRepository->insertUserTotpAuthSecret($user->getAPIUser()->id, $data['secretKey']);
                }

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
            $secretKey = $totpAuthenticator->generateSecret();
            $user->setAuthenticatorSecret($secretKey);
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
        /* @var UserTotpAuthSecret $user */
        $user = $this->getUser();

        $userRepository->deleteUserTotpAuthSecret($user->getAPIUser()->id);

        return $this->redirectToRoute('2fa_setup');
    }
}

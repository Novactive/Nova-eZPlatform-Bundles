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
use Novactive\Bundle\eZ2FABundle\Core\SiteAccessAwareAuthenticatorResolver;
use Novactive\Bundle\eZ2FABundle\Form\Type\TwoFactorAuthType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuthController extends Controller
{
    public function setupAction(
        Request $request,
        QRCodeGenerator $QRCodeGenerator,
        SiteAccessAwareAuthenticatorResolver $saAuthenticatorResolver
    ): Response {
        /* @var User $user */
        $user = $this->getUser();

        if ($saAuthenticatorResolver->checkIfUserSecretExists($user)) {
            return $this->render(
                '@ezdesign/2fa/setup.html.twig',
                [
                    'reset' => true,
                    'method' => $saAuthenticatorResolver->getMethod(),
                ]
            );
        }

        $user = $saAuthenticatorResolver->getUserAuthenticatorEntity($user);

        $form = $this->createForm(TwoFactorAuthType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($saAuthenticatorResolver->validateCodeAndUpdateUser($user, $form->getData())) {
                return $this->render(
                    '@ezdesign/2fa/setup.html.twig',
                    [
                        'success' => true,
                        'method' => $saAuthenticatorResolver->getMethod(),
                    ]
                );
            }

            $form->get('code')->addError(new FormError('Wrong code provided!'));
        }

        if (!$form->isSubmitted()) {
            $secretKey = $saAuthenticatorResolver->getAuthenticator()->generateSecret();
            $user->setAuthenticatorSecret($secretKey);
            $form->get('secretKey')->setData($secretKey);
        }

        return $this->render(
            '@ezdesign/2fa/setup.html.twig',
            [
                'qrCode' => $QRCodeGenerator->createFromUser($user),
                'form' => $form->createView(),
                'method' => $saAuthenticatorResolver->getMethod(),
            ]
        );
    }

    public function resetAction(SiteAccessAwareAuthenticatorResolver $saAuthenticatorResolver): RedirectResponse
    {
        /* @var User $user */
        $user = $this->getUser();

        $saAuthenticatorResolver->deleteUserAuthSecret($user);

        return $this->redirectToRoute('2fa_setup');
    }
}

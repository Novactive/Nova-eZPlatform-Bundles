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

declare(strict_types=1);

namespace Novactive\Bundle\eZ2FABundle\Listener;

use Ibexa\Core\MVC\Symfony\Security\User;
use Novactive\Bundle\eZ2FABundle\Core\SiteAccessAwareAuthenticatorResolver;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class OnHttpRequestListener
{
    public function __construct(
        protected TokenStorageInterface $tokenStorage,
        protected SiteAccessAwareAuthenticatorResolver $saAuthenticatorResolver,
        protected RouterInterface $router
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $setupUri = $this->router->generate('2fa_setup');

        $isMainRequestMethod = method_exists($event, 'isMainRequest') ? 'isMainRequest' : 'isMasterRequest';

        if (
            !$event->$isMainRequestMethod() || !$this->saAuthenticatorResolver->isForceSetup() ||
            $request->getRequestUri() === $setupUri
        ) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        if ($token instanceof TokenInterface) {
            $user = $token->getUser();
            if (($user instanceof User) && !$this->saAuthenticatorResolver->checkIfUserSecretOrEmailExists($user)) {
                $response = new RedirectResponse($setupUri);
                $event->setResponse($response);
            }
        }
    }
}

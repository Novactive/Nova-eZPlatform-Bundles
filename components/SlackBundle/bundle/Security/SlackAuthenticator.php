<?php

/**
 * NovaeZSlackBundle Bundle.
 *
 * @package   Novactive\Bundle\eZSlackBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZSlackBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZSlackBundle\Security;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Novactive\Bundle\eZSlackBundle\Core\Converter\User as UserConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SlackAuthenticator extends SocialAuthenticator
{
    private ClientRegistry $clientRegistry;

    private RouterInterface $router;

    private UserConverter $userConverter;

    public function __construct(ClientRegistry $clientRegistry, RouterInterface $router, UserConverter $user)
    {
        $this->clientRegistry = $clientRegistry;
        $this->router = $router;
        $this->userConverter = $user;
    }

    public function supports(Request $request): bool
    {
        $routePattern = '_novaezslack/auth/check';
        // need to manage Site Access here, then we check only the end
        return substr($request->getPathInfo(), -\strlen($routePattern)) === $routePattern;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        $user = $userProvider->loadUserByUsername(
            $this->userConverter->convert($this->getClient()->fetchUserFromToken($credentials))->login
        );
        $userProvider->refreshUser($user);

        return $user;
    }

    private function getClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient('slack');
    }

    /**
     * Returns a response that directs the user to authenticate.
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse($this->router->generate('login'));
    }

    /**
     * Called when authentication executed, but failed (e.g. wrong username password).
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new RedirectResponse($this->router->generate('login'));
    }

    /**
     * Called when authentication executed and was successfull.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): Response
    {
        $siteaccess = $request->attributes->get('siteaccess');
        /** @var SiteAccess $siteaccess */
        if ('admin' === $siteaccess->name) {
            return new RedirectResponse($this->router->generate('ezplatform.dashboard'));
        }

        return new RedirectResponse('/');
    }
}

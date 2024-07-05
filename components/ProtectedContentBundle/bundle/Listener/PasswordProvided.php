<?php

/**
 * NovaeZProtectedContentBundle.
 *
 * @package   Novactive\Bundle\eZProtectedContentBundle
 *
 * @author    Novactive
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/eZProtectedContentBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZProtectedContentBundle\Listener;

use Novactive\Bundle\eZProtectedContentBundle\Form\RequestProtectedAccessType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class PasswordProvided
{
    public const COOKIE_PREFIX = 'protected-content-';
    /**
     * @var FormFactoryInterface
     */
    public function __construct(private readonly FormFactoryInterface $formFactory)
    { }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }
        if (!$event->getRequest()->isMethod('POST')) {
            return;
        }
        $form = $this->formFactory->create(RequestProtectedAccessType::class);

        $request = $event->getRequest();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $response = new RedirectResponse($request->getRequestUri());
            $response->setPrivate();
            $data = $form->getData();
            $hash = md5($data['password']);
            $cookie = new Cookie(self::COOKIE_PREFIX.$hash, $hash, strtotime('now + 24 hours'));
            $response->headers->setCookie($cookie);
            $event->setResponse($response);
        }
    }
}

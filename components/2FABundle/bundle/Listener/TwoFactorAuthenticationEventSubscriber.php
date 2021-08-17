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

use Novactive\Bundle\eZ2FABundle\Core\SiteAccessAwareAuthenticatorResolver;
use Scheb\TwoFactorBundle\Security\TwoFactor\Event\TwoFactorAuthenticationEvent;
use Scheb\TwoFactorBundle\Security\TwoFactor\Event\TwoFactorAuthenticationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class TwoFactorAuthenticationEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var SiteAccessAwareAuthenticatorResolver
     */
    private $saAwareAuthenticatorResolver;

    public function __construct(SiteAccessAwareAuthenticatorResolver $saAwareAuthenticatorResolver)
    {
        $this->saAwareAuthenticatorResolver = $saAwareAuthenticatorResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TwoFactorAuthenticationEvents::FORM => ['onRenderAuthenticationForm', -200],
        ];
    }

    public function onRenderAuthenticationForm(TwoFactorAuthenticationEvent $event): void
    {
        $event->getToken()->setAttribute('method', $this->saAwareAuthenticatorResolver->getMethod());
    }
}

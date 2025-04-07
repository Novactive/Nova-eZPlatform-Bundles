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

namespace Novactive\Bundle\eZ2FABundle\Core\Tab;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Core\MVC\Symfony\Security\User;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\AdminUi\Tab\AbstractTab;
use Ibexa\Contracts\AdminUi\Tab\ConditionalTabInterface;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Novactive\Bundle\eZ2FABundle\Core\SiteAccessAwareAuthenticatorResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class TwoFAManagement extends AbstractTab implements OrderedTabInterface, ConditionalTabInterface
{
    public function getIdentifier(): string
    {
        return 'reset-for-user';
    }

    public function getName(): string
    {
        return '2FA';
    }

    public function getOrder(): int
    {
        return 950;
    }

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        private SiteAccessAwareAuthenticatorResolver $saAuthenticatorResolver,
        private UserService $userService,
        private PermissionResolver $permissionResolver
    ) {
        parent::__construct($twig, $translator);
    }

    public function evaluate(array $parameters): bool
    {
        /* @var ContentType $contentType */
        $contentType = $parameters['contentType'];

        return 'user' === $contentType->identifier &&
               $this->permissionResolver->hasAccess('2fa_management', 'all_functions');
    }

    public function renderView(array $parameters): string
    {
        /* @var Content $content */
        $content = $parameters['content'];

        $user = new User($this->userService->loadUser($content->id));

        return $this->twig->render(
            '@ibexadesign/2fa/tabs/reset_for_user.html.twig',
            [
                'user' => $user,
                'isSetup' => $this->saAuthenticatorResolver->checkIfUserSecretOrEmailExists($user),
                'method' => $this->saAuthenticatorResolver->getMethod(),
            ]
        );
    }
}

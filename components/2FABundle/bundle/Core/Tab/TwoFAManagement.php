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

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\MVC\Symfony\Security\User;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\ConditionalTabInterface;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use Novactive\Bundle\eZ2FABundle\Core\SiteAccessAwareAuthenticatorResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class TwoFAManagement extends AbstractTab implements OrderedTabInterface, ConditionalTabInterface
{
    /**
     * @var SiteAccessAwareAuthenticatorResolver
     */
    private $saAuthenticatorResolver;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var PermissionResolver
     */
    private $permissionResolver;

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
        SiteAccessAwareAuthenticatorResolver $saAuthenticatorResolver,
        UserService $userService,
        PermissionResolver $permissionResolver
    ) {
        parent::__construct($twig, $translator);

        $this->saAuthenticatorResolver = $saAuthenticatorResolver;
        $this->userService = $userService;
        $this->permissionResolver = $permissionResolver;
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
            '@ezdesign/2fa/tabs/reset_for_user.html.twig',
            [
                'user' => $user,
                'isSetup' => $this->saAuthenticatorResolver->checkIfUserSecretExists($user),
                'method' => $this->saAuthenticatorResolver->getMethod(),
            ]
        );
    }
}

<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtraBundle\Controller\SolrAdmin;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\NotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseController extends Controller
{
    protected PermissionResolver $permissionResolver;

    protected TranslatorInterface $translator;

    protected NotificationHandlerInterface $notificationHandler;

    #[Required]
    public function setPermissionResolver(PermissionResolver $permissionResolver): void
    {
        $this->permissionResolver = $permissionResolver;
    }

    #[Required]
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    #[Required]
    public function setNotificationHandler(NotificationHandlerInterface $notificationHandler): void
    {
        $this->notificationHandler = $notificationHandler;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    protected function permissionAccess(string $module, string $function): null
    {
        if (!$this->permissionResolver->hasAccess($module, $function)) {
            $exception = $this->createAccessDeniedException($this->translator->trans(
                'solr_admin.permission.failed',
                [],
                'solr_admin'
            ));
            $exception->setAttributes(null);
            $exception->setSubject(null);

            throw $exception;
        }

        return null;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    protected function permissionManageAccess(string $module, array $functions): array
    {
        $access = [];
        foreach ($functions as $function) {
            $access[$function] = true;
            if (!$this->permissionResolver->hasAccess($module, $function)) {
                $access[$function] = false;
            }
        }

        return $access;
    }
}

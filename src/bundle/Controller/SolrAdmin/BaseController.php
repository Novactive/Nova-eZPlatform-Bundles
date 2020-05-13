<?php

/**
 * NovaeZSolrSearchExtraBundle.
 *
 * @package   NovaeZSolrSearchExtraBundle
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZSolrSearchExtraBundle/blob/master/LICENSE
 */

namespace Novactive\EzSolrSearchExtraBundle\Controller\SolrAdmin;

use eZ\Publish\API\Repository\PermissionResolver;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\Translation\TranslatorInterface;

abstract class BaseController extends Controller
{
    /** @var PermissionResolver */
    protected $permissionResolver;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var NotificationHandlerInterface */
    protected $notificationHandler;

    /**
     * @required
     */
    public function setPermissionResolver(PermissionResolver $permissionResolver): void
    {
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * @required
     */
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @required
     */
    public function setNotificationHandler(NotificationHandlerInterface $notificationHandler): void
    {
        $this->notificationHandler = $notificationHandler;
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    protected function permissionAccess(string $module, string $function)
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
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    protected function permissionManageAccess(string $module, array $functions)
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

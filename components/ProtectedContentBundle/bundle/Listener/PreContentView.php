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

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Core\Helper\ContentPreviewHelper;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Novactive\Bundle\eZProtectedContentBundle\Entity\ProtectedAccess;
use Novactive\Bundle\eZProtectedContentBundle\Form\RequestEmailProtectedAccessType;
use Novactive\Bundle\eZProtectedContentBundle\Form\RequestProtectedAccessType;
use Novactive\Bundle\eZProtectedContentBundle\Repository\ProtectedAccessRepository;
use Novactive\Bundle\eZProtectedContentBundle\Repository\ProtectedTokenStorageRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class PreContentView
{
    public function __construct(
        protected PermissionResolver              $permissionResolver,
        protected ProtectedAccessRepository       $protectedAccessRepository,
        protected ProtectedTokenStorageRepository $protectedTokenStorageRepository,
        protected FormFactoryInterface            $factory,
        protected RequestStack                    $requestStack,
        protected ContentPreviewHelper            $contentPreviewHelper,
        protected FormFactoryInterface            $formFactory,
    )
    {
    }

    /**
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function onPreContentView(PreContentViewEvent $event): void
    {
        $contentView = $event->getContentView();

        if (!$contentView instanceof ContentView || !$contentView->getContent()) {
            return;
        }

        if ('full' !== $contentView->getViewType()) {
            return;
        }

        $content = $contentView->getContent();

        if ($content->contentInfo->isDraft()) {
            return;
        }

        if ($this->contentPreviewHelper->isPreviewActive()) {
            return;
        }

        $protections = $this->protectedAccessRepository->findByContent($content);

        if (0 === count($protections)) {
            return;
        }
        $contentView->setCacheEnabled(false);
        $canRead = $this->permissionResolver->canUser('private_content', 'read', $content);

        if (!$canRead) {
            $request = $this->requestStack->getCurrentRequest();

            if (
                $request->query->has('mail')
                && $request->query->has('token')
                && !$request->query->has('waiting_validation')
            ) {
                $unexpiredToken = $this->protectedTokenStorageRepository->findUnexpiredBy([
                    'content_id' => $content->id,
                    'token' => $request->get('token'),
                    'mail' => $request->get('mail'),
                ]);

                if (count($unexpiredToken) > 0) {
                    $canRead = true;
                }
            } else {
                $cookies = $this->requestStack->getCurrentRequest()->cookies;
                foreach ($cookies as $name => $value) {
                    $cookiePrefix = substr($name, 0, \strlen(PasswordProvided::COOKIE_PREFIX));
                    if (PasswordProvided::COOKIE_PREFIX !== $cookiePrefix) {
                        continue;
                    }
                    if (str_replace(PasswordProvided::COOKIE_PREFIX, '', $name) !== $value) {
                        continue;
                    }
                    foreach ($protections as $protection) {
                        /** @var ProtectedAccess $protection */
                        if (md5($protection->getPassword()) === $value) {
                            $canRead = true;
                        }
                    }
                }
            }
        }
        $contentView->addParameters(['canReadProtectedContent' => $canRead]);

        if (!$canRead) {
            if ('by_mail' == $this->getContentProtectionType($protections)) {
                $form = $this->formFactory->create(RequestEmailProtectedAccessType::class);
                $contentView->addParameters(['requestProtectedContentEmailForm' => $form->createView()]);
            } else {
                $form = $this->formFactory->create(RequestProtectedAccessType::class);
                $contentView->addParameters(['requestProtectedContentPasswordForm' => $form->createView()]);
            }
        }
    }

    private function getContentProtectionType(array $protections): string
    {
        foreach ($protections as $protection) {
            /** @var ProtectedAccess $protection */
            if (!is_null($protection->getPassword()) && '' != $protection->getPassword()) {
                return 'by_password';
            }
        }

        return 'by_mail';
    }
}

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
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Novactive\Bundle\eZProtectedContentBundle\Entity\ProtectedAccess;
use Novactive\Bundle\eZProtectedContentBundle\Form\RequestProtectedAccessType;
use Novactive\Bundle\eZProtectedContentBundle\Repository\ProtectedAccessRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PreContentView
{
    /**
     * @var PermissionResolver
     */
    private $permissionResolver;

    /**
     * @var ProtectedAccessRepository
     */
    private $protectedAccessRepository;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        PermissionResolver $permissionResolver,
        ProtectedAccessRepository $protectedAccessRepository,
        FormFactoryInterface $factory,
        RequestStack $requestStack
    ) {
        $this->permissionResolver = $permissionResolver;
        $this->protectedAccessRepository = $protectedAccessRepository;
        $this->formFactory = $factory;
        $this->requestStack = $requestStack;
    }

    /**
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function onPreContentView(PreContentViewEvent $event): void
    {
        $contentView = $event->getContentView();

        if (!$contentView instanceof ContentView) {
            return;
        }

        if ('full' !== $contentView->getViewType()) {
            return;
        }

        $content = $contentView->getContent();

        if ($content->contentInfo->isDraft()) {
            return;
        }

        $protections = $this->protectedAccessRepository->findByContent($content);

        if (0 === count($protections)) {
            return;
        }
        $contentView->setCacheEnabled(false);
        $canRead = $this->permissionResolver->canUser('private_content', 'read', $content);

        if (!$canRead) {
            $cookies = $this->requestStack->getCurrentRequest()->cookies;
            foreach ($cookies as $name => $value) {
                if (PasswordProvided::COOKIE_PREFIX !== substr($name, 0, \strlen(PasswordProvided::COOKIE_PREFIX))) {
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
        $contentView->addParameters(['canReadProtectedContent' => $canRead]);

        if (!$canRead) {
            $form = $this->formFactory->create(RequestProtectedAccessType::class);
            $contentView->addParameters(['requestProtectedContentPasswordForm' => $form->createView()]);
        }
    }
}

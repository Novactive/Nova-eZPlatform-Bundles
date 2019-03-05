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

use Doctrine\ORM\EntityManagerInterface;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use Novactive\Bundle\eZProtectedContentBundle\Entity\ProtectedAccess;
use Novactive\Bundle\eZProtectedContentBundle\Form\RequestProtectedAccessType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PreContentView
{
    /**
     * @var PermissionResolver
     */
    private $permissionResolver;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

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
        EntityManagerInterface $manager,
        FormFactoryInterface $factory,
        RequestStack $requestStack
    ) {
        $this->permissionResolver = $permissionResolver;
        $this->entityManager      = $manager;
        $this->formFactory        = $factory;
        $this->requestStack       = $requestStack;
    }

    public function onPreContentView(PreContentViewEvent $event)
    {
        $contentView = $event->getContentView();

        if (!$contentView instanceof ContentView) {
            return;
        }

        if ('full' !== $contentView->getViewType()) {
            return;
        }
        $content = $contentView->getContent();

        $result = $this->entityManager->getRepository(ProtectedAccess::class)->findBy(
            ['contentId' => $content->id, 'enabled' => true]
        );

        if (0 == count($result)) {
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
                foreach ($result as $item) {
                    /** @var ProtectedAccess $item */
                    if (md5($item->getPassword()) === $value) {
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

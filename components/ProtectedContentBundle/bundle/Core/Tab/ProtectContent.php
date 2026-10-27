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

namespace Novactive\Bundle\eZProtectedContentBundle\Core\Tab;

use Ibexa\Contracts\AdminUi\Tab\AbstractTab;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Core\Repository\Permission\PermissionResolver;
use Novactive\Bundle\eZProtectedContentBundle\Entity\ProtectedAccess;
use Novactive\Bundle\eZProtectedContentBundle\Form\ProtectedAccessType;
use Novactive\Bundle\eZProtectedContentBundle\Repository\ProtectedAccessRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Service\Attribute\Required;

class ProtectContent extends AbstractTab implements OrderedTabInterface
{
    private ProtectedAccessRepository $protectedAccessRepository;
    private FormFactoryInterface $formFactory;
    private PermissionResolver $permissionResolver;

    #[Required]
    public function setProtectContentRequirements(
        ProtectedAccessRepository $protectedAccessRepository,
        FormFactoryInterface $formFactory,
        PermissionResolver $permissionResolver,
    ): void
    {
        $this->protectedAccessRepository = $protectedAccessRepository;
        $this->formFactory = $formFactory;
        $this->permissionResolver = $permissionResolver;
    }

    public function getIdentifier(): string
    {
        return 'protect-content';
    }

    public function getName(): string
    {
        return $this->translator->trans('tab.header.title', [], 'ezprotectedcontent');
    }

    public function getOrder(): int
    {
        return 900;
    }

    public function renderView(array $parameters): string
    {
        $location = $parameters['location'];
        $content = $parameters['content'];
        $privateAccess = new ProtectedAccess();
        $privateAccess->setLocation($location);
        $privateAccess->setContent($content);
        $privateAccess->setContentId($content->id);
        $form = $this->formFactory->create(ProtectedAccessType::class, $privateAccess);

        $items = $this->protectedAccessRepository->findByContent($content);

        $canAddProtection = $this->permissionResolver->canUser('private_content', 'add', $location->getContent());

        return $this->twig->render(
            '@ibexadesign/tabs/protected_content.html.twig',
            [
                'form' => $form->createView(),
                'items' => $items,
                'location' => $location,
                'canAddProtection' => $canAddProtection,
            ]
        );
    }
}

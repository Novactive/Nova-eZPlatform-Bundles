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

use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use Novactive\Bundle\eZProtectedContentBundle\Core\Compose\EntityManager;
use Novactive\Bundle\eZProtectedContentBundle\Entity\ProtectedAccess;
use Novactive\Bundle\eZProtectedContentBundle\Form\ProtectedAccessType;
use Symfony\Component\Form\FormFactoryInterface;

class ProtectContent extends AbstractTab implements OrderedTabInterface
{
    use EntityManager;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @required
     */
    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

    public function getIdentifier(): string
    {
        return 'protect-content';
    }

    public function getName(): string
    {
        return 'Protect Content';
    }

    public function getOrder(): int
    {
        return 900;
    }

    public function renderView(array $parameters): string
    {
        $location      = $parameters['location'];
        $content       = $parameters['content'];
        $privateAccess = new ProtectedAccess();
        $privateAccess->setLocation($location);
        $privateAccess->setContent($content);
        $privateAccess->setContentId($content->id);
        $form = $this->formFactory->create(ProtectedAccessType::class, $privateAccess);

        $items = $this->entityManager->getRepository(ProtectedAccess::class)->findByContent($content);

        return $this->twig->render(
            '@NovaeZProtectedContent/tabs/protected_content.html.twig',
            [
                'form'     => $form->createView(),
                'items'    => $items,
                'location' => $location,
            ]
        );
    }
}

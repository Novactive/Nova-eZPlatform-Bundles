<?php
/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use eZ\Publish\Core\Helper\TranslationHelper;
use Novactive\Bundle\eZMailingBundle\Core\Provider\User as UserProvider;
use Novactive\Bundle\eZMailingBundle\Entity\MailingList;
use Novactive\Bundle\eZMailingBundle\Form\MailingListType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class MailingListController.
 *
 * @Route("/mailinglist")
 */
class MailingListController
{
    /**
     * @Route("/show/{mailingList}/{status}/{page}/{limit}", name="novaezmailing_mailinglist_show",
     *                                              defaults={"page":1, "limit":10, "status":"all"})
     * @Security("is_granted('view', mailingList)")
     * @Template()
     *
     * @return array
     */
    public function showAction(
        MailingList $mailingList,
        UserProvider $provider,
        string $status = 'all',
        int $page = 1,
        int $limit = 10
    ): array {
        $filers = [
            'mailingLists' => [$mailingList],
            'status'       => 'all' === $status ? null : (int) $status,
        ];

        return [
            'pager'         => $provider->getPagerFilters($filers, $page, $limit),
            'item'          => $mailingList,
            'statuses'      => $provider->getStatusesData($filers),
            'currentStatus' => $status,
        ];
    }

    /**
     * @Route("", name="novaezmailing_mailinglist_index")
     * @Template()
     *
     * @param EntityManagerInterface $entityManager
     *
     * @return array
     */
    public function indexAction(EntityManagerInterface $entityManager): array
    {
        $repo = $entityManager->getRepository(MailingList::class);

        return ['items' => $repo->findAll()];
    }

    /**
     * @Route("/edit/{mailinglist}", name="novaezmailing_mailinglist_edit")
     * @Route("/create", name="novaezmailing_mailinglist_create")
     * @Security("is_granted('edit', mailinglist)")
     * @Template()
     *
     * @param MailingList|null       $mailing
     * @param Request                $request
     * @param RouterInterface        $router
     * @param FormFactoryInterface   $formFactory
     * @param EntityManagerInterface $entityManager
     * @param TranslationHelper      $translationHelper
     *
     * @return array|RedirectResponse
     */
    public function editAction(
        ?MailingList $mailinglist,
        Request $request,
        RouterInterface $router,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        TranslationHelper $translationHelper
    ) {
        if (null === $mailinglist) {
            $mailinglist = new MailingList();
            $languages   = $translationHelper->getAvailableLanguages();
            $mailinglist->setNames(array_combine($languages, array_pad([], count($languages), '')));
        }

        $form = $formFactory->create(MailingListType::class, $mailinglist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($mailinglist);
            $entityManager->flush();

            return new RedirectResponse(
                $router->generate('novaezmailing_mailinglist_show', ['mailingList' => $mailinglist->getId()])
            );
        }

        return [
            'item' => $mailinglist,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/delete/{mailinglist}", name="novaezmailing_mailinglist_remove")
     * @Security("is_granted('edit', mailinglist)")
     *
     * @param MailingList            $campaign
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     *
     * @return RedirectResponse
     */
    public function deleteAction(
        MailingList $mailinglist,
        EntityManagerInterface $entityManager,
        RouterInterface $router
    ): RedirectResponse {
        $entityManager->remove($mailinglist);
        $entityManager->flush();

        return new RedirectResponse($router->generate('novaezmailing_mailinglist_index'));
    }
}

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

use Doctrine\ORM\EntityManager;
use Novactive\Bundle\eZMailingBundle\Core\Mailer\Mailing;
use Novactive\Bundle\eZMailingBundle\Core\Mailer\Simple;
use Novactive\Bundle\eZMailingBundle\Entity\MailingList;
use Novactive\Bundle\eZMailingBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class DashboardController
{
    /**
     * @Route("", name="novaezmailing_dashboard_index")
     * @Template()
     *
     * @return Response
     */
    public function indexAction(Simple $simpleMailer, Mailing $mailingMailer): array
    {
        dump($simpleMailer);
        dump($mailingMailer);
        return [];
    }

    /**
     * @Route("/search/autocomplete", name="novaezmailing_dashboard_search_autocomplete")
     *
     * @return JsonResponse
     */
    public function autocompleteSearchAction(
        Request $request,
        RouterInterface $router,
        EntityManager $entityManager
    ): JsonResponse {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse('Not Authorized', 403);
        }

        $query = $request->query->get('query');

        $repo  = $entityManager->getRepository(User::class);
        $users = $repo->findByFilters(['query' => $query]);

        $userResults = array_map(
            function (User $user) use ($router) {
                return [
                    'value' => trim($user->getGender().' '.$user->getFirstName().' '.$user->getLastName()),
                    'data'  => $router->generate('novaezmailing_user_show', ['user' => $user->getId()]),
                ];
            },
            $users
        );

        $repo               = $entityManager->getRepository(MailingList::class);
        $mailingLists       = $repo->findByFilters(['query' => $query]);
        $mailingListResults = array_map(
            function (MailingList $mailingList) use ($router) {
                return [
                    'value' => trim($mailingList->getName()),
                    'data'  => $router->generate(
                        'novaezmailing_mailinglist_show',
                        ['mailingList' => $mailingList->getId()]
                    ),
                ];
            },
            $mailingLists
        );

        return new JsonResponse(['suggestions' => $userResults + $mailingListResults]);
    }
}

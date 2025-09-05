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
use Novactive\Bundle\eZMailingBundle\Core\Provider\User as UserProvider;
use Novactive\Bundle\eZMailingBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route("/user")
 */
class UserController
{
    /**
     * @Route("/show/{user}", name="novaezmailing_user_show")
     *
     * @Template()
     */
    public function showAction(User $user): array
    {
        if ($user->isRestricted()) {
            throw new AccessDeniedHttpException('User has been restricted');
        }

        return [
            'item' => $user,
        ];
    }

    /**
     * @Route("/delete/{user}", name="novaezmailing_user_remove")
     */
    public function deleteAction(
        User $user,
        EntityManagerInterface $entityManager,
        RouterInterface $router
    ): RedirectResponse {
        $entityManager->remove($user);
        $entityManager->flush();

        return new RedirectResponse($router->generate('novaezmailing_user_index'));
    }

    /**
     * @Route("/{status}/{page}/{limit}", name="novaezmailing_user_index",
     *                                              defaults={"page":1, "limit":10, "status":"all"})
     *
     * @Template()
     */
    public function indexAction(UserProvider $provider, string $status = 'all', int $page = 1, int $limit = 10): array
    {
        $filters = [
            'status' => 'all' === $status ? null : $status,
        ];

        return [
            'pager' => $provider->getPagerFilters($filters, $page, $limit),
            'statuses' => $provider->getStatusesData($filters),
            'currentStatus' => $status,
        ];
    }
}

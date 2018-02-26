<?php
/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/eZMailingBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Controller\Admin;

use Novactive\Bundle\eZMailingBundle\Core\Provider\User as UserProvider;
use Novactive\Bundle\eZMailingBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class UserController.
 *
 * @Route("/user")
 */
class UserController
{
    /**
     * @Route("/show/{user}", name="novaezmailing_user_show")
     * @Template()
     *
     * @return array
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
     * @Route("/{status}/{page}/{limit}", name="novaezmailing_user_index",
     *                                              defaults={"page":1, "limit":10, "status":"all"})
     * @Template()
     *
     * @return array
     */
    public function indexAction(UserProvider $provider, string $status = 'all', int $page = 1, int $limit = 10): array
    {
        $filers = [
            'status' => 'all' === $status ? null : (int) $status,
        ];

        return [
            'pager'         => $provider->getPagerFilters($filers, $page, $limit),
            'statuses'      => $provider->getStatusesData($filers),
            'currentStatus' => $status,
        ];
    }
}

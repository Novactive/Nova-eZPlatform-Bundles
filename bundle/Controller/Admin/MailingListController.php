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
use Novactive\Bundle\eZMailingBundle\Core\Provider\User as UserProvider;
use Novactive\Bundle\eZMailingBundle\Entity\MailingList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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
     * @return array
     */
    public function indexAction(EntityManager $entityManager): array
    {
        $repo = $entityManager->getRepository(MailingList::class);

        return ['items' => $repo->findAll()];
    }
}

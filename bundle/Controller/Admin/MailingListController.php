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

use Doctrine\ORM\EntityManager;
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

    /**
     * @Route("/show/{mailingList}", name="novaezmailing_mailinglist_show")
     * @Template()
     *
     * @return array
     */
    public function showAction(MailingList $mailingList): array
    {
        return [
            'item' => $mailingList,
        ];
    }
}

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
use Novactive\Bundle\eZMailingBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class UserController.
 *
 * @Route("/user")
 */
class UserController
{
    /**
     * @Route("", name="novaezmailing_user_index")
     * @Template()
     *
     * @return array
     */
    public function indexAction(EntityManager $entityManager): array
    {
        $repo = $entityManager->getRepository(User::class);

        return ['items' => $repo->findAll()];
    }
}

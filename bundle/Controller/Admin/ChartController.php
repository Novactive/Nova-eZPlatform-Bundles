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
use Novactive\Bundle\eZMailingBundle\Core\Utils\ChartDataBuilder;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use Novactive\Bundle\eZMailingBundle\Entity\Registration;
use Novactive\Bundle\eZMailingBundle\Entity\StatHit;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class ChartController.
 */
class ChartController
{
    /**
     * @param int           $mailingId
     * @param EntityManager $entityManager
     *
     * @Template("NovaeZMailingBundle:admin/chart:generic.html.twig")
     *
     * @return array
     */
    public function browserChart(int $mailingId, EntityManager $entityManager): array
    {
        $mailingRepo = $entityManager->getRepository(Mailing::class);
        $mailing     = $mailingRepo->findOneById($mailingId);
        $hitRepo     = $entityManager->getRepository(StatHit::class);
        $data        = $hitRepo->getBrowserMapCount([$mailing]);

        $chartBuilder = new ChartDataBuilder('Browser Repartition', 'doughnut');
        $chartBuilder->addDataSet(array_values($data), array_keys($data));

        return ['chart' => $chartBuilder()];
    }

    /**
     * @param int           $mailingId
     * @param EntityManager $entityManager
     *
     * @Template("NovaeZMailingBundle:admin/chart:generic.html.twig")
     *
     * @return array
     */
    public function osChart(int $mailingId, EntityManager $entityManager): array
    {
        $mailingRepo = $entityManager->getRepository(Mailing::class);
        $mailing     = $mailingRepo->findOneById($mailingId);
        $hitRepo     = $entityManager->getRepository(StatHit::class);
        $data        = $hitRepo->getOSMapCount([$mailing]);

        $chartBuilder = new ChartDataBuilder('OS Repartition', 'doughnut');
        $chartBuilder->addDataSet(array_values($data), array_keys($data));

        return ['chart' => $chartBuilder()];
    }

    /**
     * @param int           $mailingId
     * @param EntityManager $entityManager
     *
     * @Template("NovaeZMailingBundle:admin/chart:generic.html.twig")
     *
     * @return array
     */
    public function urlChart(int $mailingId, EntityManager $entityManager): array
    {
        $mailingRepo = $entityManager->getRepository(Mailing::class);
        $mailing     = $mailingRepo->findOneById($mailingId);
        $hitRepo     = $entityManager->getRepository(StatHit::class);
        $data        = $hitRepo->getURLMapCount([$mailing]);

        $chartBuilder = new ChartDataBuilder('URLs Clicked Repartition', 'pie');
        $chartBuilder->addDataSet(array_values($data), array_keys($data));

        return ['chart' => $chartBuilder()];
    }

    /**
     * @param int           $mailingId
     * @param EntityManager $entityManager
     *
     * @Template("NovaeZMailingBundle:admin/chart:generic.html.twig")
     *
     * @return array
     */
    public function openedChart(int $mailingId, EntityManager $entityManager): array
    {
        $mailingRepo                = $entityManager->getRepository(Mailing::class);
        $registrationRepo           = $entityManager->getRepository(Registration::class);
        $mailing                    = $mailingRepo->findOneById($mailingId);
        $hitRepo                    = $entityManager->getRepository(StatHit::class);
        $openedCount                = $hitRepo->getOpenedCount([[$mailing]]);
        $approvedRegistrationtCount = $registrationRepo->getApprovedCount([$mailing]);
        $data                       = [
            'Opened'     => $openedCount,
            'Not Opened' => $approvedRegistrationtCount - $openedCount,
        ];

        $chartBuilder = new ChartDataBuilder('Opened emails', 'pie');
        $chartBuilder->addDataSet(array_values($data), array_keys($data));

        return ['chart' => $chartBuilder()];
    }
}

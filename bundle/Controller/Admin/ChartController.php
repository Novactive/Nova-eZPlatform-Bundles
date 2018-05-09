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
use Novactive\Bundle\eZMailingBundle\Core\Utils\ChartDataBuilder;
use Novactive\Bundle\eZMailingBundle\Entity\Broadcast;
use Novactive\Bundle\eZMailingBundle\Entity\StatHit;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class ChartController.
 */
class ChartController
{
    /**
     * @param int                    $broadcastId
     * @param EntityManagerInterface $entityManager
     *
     * @Template("NovaeZMailingBundle:admin/chart:generic.html.twig")
     *
     * @return array
     */
    public function browserChart(int $broadcastId, EntityManagerInterface $entityManager): array
    {
        $repo = $entityManager->getRepository(Broadcast::class);
        /** @var Broadcast $item */
        $item    = $repo->findOneById($broadcastId);
        $hitRepo = $entityManager->getRepository(StatHit::class);
        $data    = $hitRepo->getBrowserMapCount([$item]);

        $chartBuilder = new ChartDataBuilder('Browser Repartition', 'doughnut');
        $chartBuilder->addDataSet(array_values($data), array_keys($data));

        return ['chart' => $chartBuilder()];
    }

    /**
     * @param int                    $broadcastId
     * @param EntityManagerInterface $entityManager
     *
     * @Template("NovaeZMailingBundle:admin/chart:generic.html.twig")
     *
     * @return array
     */
    public function osChart(int $broadcastId, EntityManagerInterface $entityManager): array
    {
        $repo = $entityManager->getRepository(Broadcast::class);
        /** @var Broadcast $item */
        $item    = $repo->findOneById($broadcastId);
        $hitRepo = $entityManager->getRepository(StatHit::class);

        $data = $hitRepo->getOSMapCount([$item]);

        $chartBuilder = new ChartDataBuilder('OS Repartition', 'doughnut');
        $chartBuilder->addDataSet(array_values($data), array_keys($data));

        return ['chart' => $chartBuilder()];
    }

    /**
     * @param int                    $broadcastId
     * @param EntityManagerInterface $entityManager
     *
     * @Template("NovaeZMailingBundle:admin/chart:generic.html.twig")
     *
     * @return array
     */
    public function urlChart(int $broadcastId, EntityManagerInterface $entityManager): array
    {
        $repo = $entityManager->getRepository(Broadcast::class);
        /** @var Broadcast $item */
        $item    = $repo->findOneById($broadcastId);
        $hitRepo = $entityManager->getRepository(StatHit::class);

        $data = $hitRepo->getURLMapCount([$item]);

        $chartBuilder = new ChartDataBuilder('URLs Clicked Repartition', 'pie');
        $chartBuilder->addDataSet(array_values($data), array_keys($data));

        return ['chart' => $chartBuilder()];
    }

    /**
     * @param int                    $broadcastId
     * @param EntityManagerInterface $entityManager
     *
     * @Template("NovaeZMailingBundle:admin/chart:generic.html.twig")
     *
     * @return array
     */
    public function openedChart(int $broadcastId, EntityManagerInterface $entityManager): array
    {
        $repo = $entityManager->getRepository(Broadcast::class);
        /** @var Broadcast $item */
        $item           = $repo->findOneById($broadcastId);
        $hitRepo        = $entityManager->getRepository(StatHit::class);
        $openedCount    = $hitRepo->getOpenedCount([[$item]]);
        $broadcastCount = $item->getEmailSentCount();
        $data           = [
            'Opened'     => $openedCount,
            'Not Opened' => $broadcastCount - $openedCount,
        ];

        $chartBuilder = new ChartDataBuilder('Opened emails', 'pie');
        $chartBuilder->addDataSet(array_values($data), array_keys($data));

        return ['chart' => $chartBuilder()];
    }
}

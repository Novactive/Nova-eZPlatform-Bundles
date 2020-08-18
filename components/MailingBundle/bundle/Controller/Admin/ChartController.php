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
     * @Template("NovaeZMailingBundle:admin/chart:generic.html.twig")
     */
    public function browserChart(int $broadcastId, EntityManagerInterface $entityManager): array
    {
        $repo = $entityManager->getRepository(Broadcast::class);
        /** @var Broadcast $item */
        $item = $repo->findOneById($broadcastId);
        $hitRepo = $entityManager->getRepository(StatHit::class);
        $data = $hitRepo->getBrowserMapCount([$item]);

        $chartBuilder = new ChartDataBuilder('Browser Repartition', 'doughnut');
        $chartBuilder->addDataSet(array_values($data), array_keys($data));

        return ['chart' => $chartBuilder()];
    }

    /**
     * @Template("NovaeZMailingBundle:admin/chart:generic.html.twig")
     */
    public function osChart(int $broadcastId, EntityManagerInterface $entityManager): array
    {
        $repo = $entityManager->getRepository(Broadcast::class);
        /** @var Broadcast $item */
        $item = $repo->findOneById($broadcastId);
        $hitRepo = $entityManager->getRepository(StatHit::class);

        $data = $hitRepo->getOSMapCount([$item]);

        $chartBuilder = new ChartDataBuilder('OS Repartition', 'doughnut');
        $chartBuilder->addDataSet(array_values($data), array_keys($data));

        return ['chart' => $chartBuilder()];
    }

    /**
     * @Template("NovaeZMailingBundle:admin/chart:generic.html.twig")
     */
    public function urlChart(int $broadcastId, EntityManagerInterface $entityManager): array
    {
        $repo = $entityManager->getRepository(Broadcast::class);
        /** @var Broadcast $item */
        $item = $repo->findOneById($broadcastId);
        $hitRepo = $entityManager->getRepository(StatHit::class);

        $data = $hitRepo->getURLMapCount([$item]);

        $chartBuilder = new ChartDataBuilder('URLs Clicked Repartition', 'pie');
        $chartBuilder->addDataSet(array_values($data), array_keys($data));

        return ['chart' => $chartBuilder()];
    }

    /**
     * @Template("NovaeZMailingBundle:admin/chart:generic.html.twig")
     */
    public function openedChart(int $broadcastId, EntityManagerInterface $entityManager): array
    {
        $repo = $entityManager->getRepository(Broadcast::class);
        /** @var Broadcast $item */
        $item = $repo->findOneById($broadcastId);
        $hitRepo = $entityManager->getRepository(StatHit::class);
        $openedCount = $hitRepo->getOpenedCount([[$item]]);
        $broadcastCount = $item->getEmailSentCount();
        $data = [
            'Opened' => $openedCount,
            'Not Opened' => $broadcastCount - $openedCount,
        ];

        $chartBuilder = new ChartDataBuilder('Opened emails', 'pie');
        $chartBuilder->addDataSet(array_values($data), array_keys($data));

        return ['chart' => $chartBuilder()];
    }

    /**
     * @Template("NovaeZMailingBundle:admin/chart:generic.html.twig")
     */
    public function openedTimeChart(int $broadcastId, EntityManagerInterface $entityManager): array
    {
        $repo = $entityManager->getRepository(Broadcast::class);
        /** @var Broadcast $item */
        $item = $repo->findOneById($broadcastId);
        $hitRepo = $entityManager->getRepository(StatHit::class);
        $data = $hitRepo->getOpenedCountPerDay([[$item]]);

        $chartBuilder = new ChartDataBuilder('Opened per day', 'bar');
        $values = array_values($data);
        $chartBuilder->addDataSet($values, array_keys($data), array_pad([], count($values), '#36a2eb'));
        $chartBuilder->addDataSet($values, array_keys($data), ['#ff6384'], 'line');

        return ['chart' => $chartBuilder()];
    }
}

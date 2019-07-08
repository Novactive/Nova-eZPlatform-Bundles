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

namespace Novactive\Bundle\eZMailingBundle\Core\Provider;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Novactive\Bundle\eZMailingBundle\Entity\Broadcast as BroadcastEntity;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;

/**
 * Class Broadcast.
 */
class Broadcast
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Broadcast constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Mailing $mailing
     * @param string  $html
     *
     * @return BroadcastEntity
     */
    public function start(Mailing $mailing, string $html): BroadcastEntity
    {
        $broadcast = new BroadcastEntity();
        $broadcast
            ->setMailing($mailing)
            ->setStarted(Carbon::now())
            ->setHtml($html)
            ->setCreated(new \DateTime())
            ->setUpdated(new \DateTime());
        $this->store($broadcast);

        return $broadcast;
    }

    /**
     * @param BroadcastEntity $broadcast
     */
    public function end(BroadcastEntity $broadcast): void
    {
        $broadcast->setEnded(Carbon::now());
        $this->store($broadcast);
    }

    /**
     * @param BroadcastEntity $broadcast
     */
    public function store(BroadcastEntity $broadcast): void
    {
        $this->entityManager->persist($broadcast);
        $this->entityManager->flush();
    }
}

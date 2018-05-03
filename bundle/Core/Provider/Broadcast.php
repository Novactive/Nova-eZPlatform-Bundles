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
use Doctrine\ORM\EntityManager;
use Novactive\Bundle\eZMailingBundle\Entity\Broadcast as BroadcastEntity;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;

/**
 * Class Broadcast.
 */
class Broadcast
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Broadcast constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
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
        $broadcast->setMailing($mailing);
        $broadcast->setStarted(Carbon::now());
        $broadcast->setHtml($html);
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

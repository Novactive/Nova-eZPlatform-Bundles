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

namespace Novactive\Bundle\eZMailingBundle\Entity\Compose;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait Remote.
 */
trait Remote
{
    /**
     * @var string
     * @ORM\Column(name="OBJ_remote_id", type="string", length=255, nullable=true)
     */
    private $remoteId;

    /**
     * @var DateTime
     * @ORM\Column(name="OBJ_remote_last_synchro", type="datetime", nullable=true)
     */
    private $lastSynchro;

    /**
     * @var int
     * @ORM\Column(name="OBJ_remote_status", type="smallint", nullable=true)
     */
    private $remoteStatus;

    /**
     * @return string
     */
    public function getRemoteId(): ?string
    {
        return $this->remoteId;
    }

    /**
     * @param string $remoteId
     *
     * @return Remote
     */
    public function setRemoteId(string $remoteId): self
    {
        $this->remoteId = $remoteId;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getLastSynchro(): ?DateTime
    {
        return $this->lastSynchro;
    }

    /**
     * @param DateTime $lastSynchro
     *
     * @return Remote
     */
    public function setLastSynchro(DateTime $lastSynchro): self
    {
        $this->lastSynchro = $lastSynchro;

        return $this;
    }

    /**
     * @return int
     */
    public function getRemoteStatus(): int
    {
        return $this->remoteStatus;
    }

    /**
     * @param int $remoteStatus
     *
     * @return Remote
     */
    public function setRemoteStatus(int $remoteStatus): self
    {
        $this->remoteStatus = $remoteStatus;

        return $this;
    }
}

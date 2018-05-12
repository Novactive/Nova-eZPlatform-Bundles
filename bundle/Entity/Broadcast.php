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

namespace Novactive\Bundle\eZMailingBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Broadcast.
 *
 * A Broadcast is a record of a Mailing "sending" at a certain point in time
 * to a certain number of approved registrations
 * with a certain html contents (we will backup here)
 * It's really a record of a Mailing broadcast
 *
 * @ORM\Table(name="novaezmailing_broadcast")
 *
 * @ORM\Entity(repositoryClass="Novactive\Bundle\eZMailingBundle\Repository\Broadcast")
 */
class Broadcast
{
    use Compose\Metadata;

    /**
     * @var int
     * @ORM\Column(name="BDCST_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var DateTime
     * @ORM\Column(name="BDCST_started", type="datetime", nullable=false)
     */
    private $started;

    /**
     * @var DateTime
     * @ORM\Column(name="BDCST_ended", type="datetime", nullable=true)
     */
    private $ended;

    /**
     * @var int
     * @ORM\Column(name="BDCST_email_sent_count", type="integer", nullable=false)
     */
    private $emailSentCount;

    /**
     * @var string
     * @ORM\Column(name="BDCST_html", type="text", nullable=false)
     */
    private $html;

    /**
     * @var Mailing
     * @ORM\ManyToOne(targetEntity="Novactive\Bundle\eZMailingBundle\Entity\Mailing", inversedBy="broadcasts")
     * @ORM\JoinColumn(name="MAIL_id", referencedColumnName="MAIL_id")
     */
    private $mailing;

    /**
     * @var StatHit[]
     * @ORM\OneToMany(targetEntity="Novactive\Bundle\eZMailingBundle\Entity\StatHit", mappedBy="broadcast",
     *                                                                                cascade={"persist","remove"},
     *                                                                                fetch="EXTRA_LAZY")
     */
    private $statHits;

    /**
     * Broadcast constructor.
     */
    public function __construct()
    {
        $this->emailSentCount = 0;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStarted(): DateTime
    {
        return $this->started;
    }

    /**
     * @param DateTime $started
     *
     * @return $this
     */
    public function setStarted(DateTime $started): self
    {
        $this->started = $started;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEnded(): DateTime
    {
        return $this->ended;
    }

    /**
     * @param DateTime $ended
     *
     * @return $this
     */
    public function setEnded(DateTime $ended): self
    {
        $this->ended = $ended;

        return $this;
    }

    /**
     * @return int
     */
    public function getEmailSentCount(): int
    {
        return $this->emailSentCount;
    }

    /**
     * @param int $emailSentCount
     *
     * @return $this
     */
    public function setEmailSentCount(int $emailSentCount): self
    {
        $this->emailSentCount = $emailSentCount;

        return $this;
    }

    /**
     * @return Mailing
     */
    public function getMailing(): Mailing
    {
        return $this->mailing;
    }

    /**
     * @param Mailing $mailing
     *
     * @return $this
     */
    public function setMailing(Mailing $mailing): self
    {
        $this->mailing = $mailing;

        return $this;
    }

    /**
     * @return string
     */
    public function getHtml(): string
    {
        return $this->html;
    }

    /**
     * @param string $html
     *
     * @return $this
     */
    public function setHtml(string $html): self
    {
        $this->html = $html;

        return $this;
    }

    /**
     * @return StatHit[]
     */
    public function getStatHits(): array
    {
        return $this->statHits;
    }
}

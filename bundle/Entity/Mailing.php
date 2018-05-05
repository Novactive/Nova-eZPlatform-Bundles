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

use Carbon\Carbon;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Novactive\Bundle\eZMailingBundle\Core\Utils\Clock;
use Novactive\Bundle\eZMailingBundle\Validator\Constraints as NovaEzMailingAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Mailing.
 *
 * @ORM\Table(name="novaezmailing_mailing")
 *
 * @ORM\Entity(repositoryClass="Novactive\Bundle\eZMailingBundle\Repository\Mailing")
 * @ORM\EntityListeners({"Novactive\Bundle\eZMailingBundle\Listener\EntityContentLink"})
 */
class Mailing implements eZ\ContentInterface
{
    use Compose\Metadata;
    use Compose\Names;
    use eZ\Content;

    /**
     * Just created.
     */
    const DRAFT = 'draft';

    /**
     * Ready to be sent.
     */
    const PENDING = 'pending';

    /**
     * Currently Processing.
     */
    const PROCESSING = 'processing';

    /**
     * Sent, Processing over.
     */
    const SENT = 'sent';

    /**
     * Aborted.
     */
    const ABORTED = 'aborted';

    /**
     * Archived.
     */
    const ARCHIVED = 'archived';

    /**
     * Statuses.
     */
    const STATUSES = [
        self::DRAFT,
        self::PENDING,
        self::PROCESSING,
        self::SENT,
        self::ABORTED,
        self::ARCHIVED,
    ];

    /**
     * @var int
     * @ORM\Column(name="MAIL_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="MAIL_status", type="string", nullable=false)
     */
    private $status;

    /**
     * @var bool
     * @ORM\Column(name="MAIL_recurring", type="boolean", nullable=false)
     */
    private $recurring;

    /**
     * @var array
     * @NovaEzMailingAssert\ArrayRange(min=0,max=24)
     * @ORM\Column(name="MAIL_hours_of_day", type="array", nullable=false)
     */
    private $hoursOfDay;

    /**
     * @var array
     * @NovaEzMailingAssert\ArrayRange(min=1,max=7)
     * @ORM\Column(name="MAIL_days_of_week", type="array", nullable=true)
     */
    private $daysOfWeek;

    /**
     * @var array
     * @NovaEzMailingAssert\ArrayRange(min=1,max=31)
     * @ORM\Column(name="MAIL_days_of_month", type="array", nullable=true)
     */
    private $daysOfMonth;

    /**
     * @var array
     * @NovaEzMailingAssert\ArrayRange(min=1,max=365)
     * @ORM\Column(name="MAIL_days_of_year", type="array", nullable=true)
     */
    private $daysOfYear;

    /**
     * @var array
     * @NovaEzMailingAssert\ArrayRange(min=1,max=5)
     * @ORM\Column(name="MAIL_weeks_of_month", type="array", nullable=true)
     */
    private $weeksOfMonth;

    /**
     * @var array
     * @NovaEzMailingAssert\ArrayRange(min=1,max=12)
     * @ORM\Column(name="MAIL_months_of_year", type="array", nullable=true)
     */
    private $monthsOfYear;

    /**
     * @var array
     * @NovaEzMailingAssert\ArrayRange(min=1,max=53)
     * @ORM\Column(name="MAIL_weeks_of_year", type="array", nullable=true)
     */
    private $weeksOfYear;

    /**
     * @var Campaign
     * @ORM\ManyToOne(targetEntity="Novactive\Bundle\eZMailingBundle\Entity\Campaign", inversedBy="mailings")
     * @ORM\JoinColumn(name="CAMP_id", referencedColumnName="CAMP_id")
     */
    private $campaign;

    /**
     * @var Broadcast[]
     * @ORM\OneToMany(targetEntity="Novactive\Bundle\eZMailingBundle\Entity\Broadcast", mappedBy="mailing",
     *                                                                                  cascade={"persist","remove"})
     */
    private $broadcasts;

    /**
     * Mailing constructor.
     */
    public function __construct()
    {
        $this->recurring    = false;
        $this->statHits     = new ArrayCollection();
        $this->broadcasts   = new ArrayCollection();
        $this->hoursOfDay   = [];
        $this->daysOfWeek   = [];
        $this->daysOfMonth  = [];
        $this->daysOfYear   = [];
        $this->weeksOfMonth = [];
        $this->monthsOfYear = [];
        $this->weeksOfYear  = [];
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
     * @return Mailing
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return Mailing
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getLastSent(): ?DateTime
    {
        if (0 == $this->broadcasts->count()) {
            return null;
        }
        $lastSent = Carbon::now();
        foreach ($this->broadcasts as $broadcast) {
            /** @var Broadcast $broadcast */
            if ($lastSent->getTimestamp() < $broadcast->getStarted()->getTimestamp()) {
                $lastSent = $broadcast->getStarted();
            }
        }

        return $lastSent;
    }

    /**
     * @return bool
     */
    public function isRecurring(): bool
    {
        return $this->recurring;
    }

    /**
     * @param bool $recurring
     *
     * @return Mailing
     */
    public function setRecurring(bool $recurring): self
    {
        $this->recurring = $recurring;

        return $this;
    }

    /**
     * @return array
     */
    public function getHoursOfDay(): array
    {
        return $this->hoursOfDay;
    }

    /**
     * @param array $hoursOfDay
     *
     * @return Mailing
     */
    public function setHoursOfDay(array $hoursOfDay): self
    {
        $this->hoursOfDay = $hoursOfDay;

        return $this;
    }

    /**
     * @return array
     */
    public function getDaysOfWeek(): array
    {
        return $this->daysOfWeek;
    }

    /**
     * @param array $daysOfWeek
     *
     * @return Mailing
     */
    public function setDaysOfWeek(array $daysOfWeek): self
    {
        $this->daysOfWeek = $daysOfWeek;

        return $this;
    }

    /**
     * @return array
     */
    public function getDaysOfMonth(): array
    {
        return $this->daysOfMonth;
    }

    /**
     * @param array $daysOfMonth
     *
     * @return Mailing
     */
    public function setDaysOfMonth(array $daysOfMonth): self
    {
        $this->daysOfMonth = $daysOfMonth;

        return $this;
    }

    /**
     * @return array
     */
    public function getDaysOfYear(): array
    {
        return $this->daysOfYear;
    }

    /**
     * @param array $daysOfYear
     *
     * @return Mailing
     */
    public function setDaysOfYear(array $daysOfYear): self
    {
        $this->daysOfYear = $daysOfYear;

        return $this;
    }

    /**
     * @return array
     */
    public function getWeeksOfMonth(): array
    {
        return $this->weeksOfMonth;
    }

    /**
     * @param array $weeksOfMonth
     *
     * @return Mailing
     */
    public function setWeeksOfMonth(array $weeksOfMonth): self
    {
        $this->weeksOfMonth = $weeksOfMonth;

        return $this;
    }

    /**
     * @return array
     */
    public function getMonthsOfYear(): array
    {
        return $this->monthsOfYear;
    }

    /**
     * @param array $monthsOfYear
     *
     * @return Mailing
     */
    public function setMonthsOfYear(array $monthsOfYear): self
    {
        $this->monthsOfYear = $monthsOfYear;

        return $this;
    }

    /**
     * @return array
     */
    public function getWeeksOfYear(): array
    {
        return $this->weeksOfYear;
    }

    /**
     * @param array $weeksOfYear
     *
     * @return Mailing
     */
    public function setWeeksOfYear(array $weeksOfYear): self
    {
        $this->weeksOfYear = $weeksOfYear;

        return $this;
    }

    /**
     * @return Campaign
     */
    public function getCampaign(): Campaign
    {
        return $this->campaign;
    }

    /**
     * @param Campaign $campaign
     *
     * @return Mailing
     */
    public function setCampaign(?Campaign $campaign): self
    {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function nextTick(): ?DateTime
    {
        try {
            $clock = new Clock(Carbon::now());

            return $clock->nextTick($this);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return bool
     */
    public function hasBeenSent(): bool
    {
        return
            (false === $this->isRecurring() && self::SENT === $this->status) ||
            (true === $this->isRecurring() && null !== $this->getLastSent());
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return self::PENDING === $this->status;
    }

    /**
     * @return bool
     */
    public function isDraft(): bool
    {
        return self::DRAFT === $this->status;
    }

    /**
     * @return bool
     */
    public function isArchived(): bool
    {
        return self::ARCHIVED === $this->status;
    }

    /**
     * @return bool
     */
    public function isAborted(): bool
    {
        return self::ABORTED === $this->status;
    }

    /**
     * @return bool
     */
    public function isProcessing(): bool
    {
        return self::PROCESSING === $this->status;
    }

    /**
     * @return mixed
     */
    public function getBroadcasts()
    {
        return $this->broadcasts;
    }

    /**
     * @param Broadcast[] $broadcasts
     *
     * @return $this
     */
    public function setBroadcasts(array $broadcasts): self
    {
        $this->broadcasts = $broadcasts;

        return $this;
    }

    /**
     * @param Broadcast $broadcast
     *
     * @return $this
     */
    public function addBroadcast(Broadcast $broadcast): self
    {
        if ($this->broadcasts->contains($broadcast)) {
            return $this;
        }
        $this->broadcasts->add($broadcast);
        $broadcast->setMailing($this);

        return $this;
    }
}

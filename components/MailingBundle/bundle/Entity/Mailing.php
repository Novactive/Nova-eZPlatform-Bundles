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
    public const DRAFT = 'draft';

    /**
     * Tested.
     */
    public const TESTED = 'tested';

    /**
     * Ready to be sent.
     */
    public const PENDING = 'pending';

    /**
     * Currently Processing.
     */
    public const PROCESSING = 'processing';

    /**
     * Sent, Processing over.
     */
    public const SENT = 'sent';

    /**
     * Aborted.
     */
    public const ABORTED = 'aborted';

    /**
     * Archived.
     */
    public const ARCHIVED = 'archived';

    /**
     * Statuses.
     */
    public const STATUSES = [
        self::DRAFT,
        self::TESTED,
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
     *                                                                                  cascade={"persist","remove"},
     *                                                                                  fetch="EXTRA_LAZY")
     */
    private $broadcasts;

    /**
     * @var string
     * @ORM\Column(name="MAIL_subject", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $subject;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="MAIL_siteaccess", type="string", nullable=false)
     */
    private $siteAccess;

    public function __construct()
    {
        $this->recurring = false;
        $this->statHits = new ArrayCollection();
        $this->broadcasts = new ArrayCollection();
        $this->created = new DateTime();
        $this->hoursOfDay = [];
        $this->daysOfWeek = [];
        $this->daysOfMonth = [];
        $this->daysOfYear = [];
        $this->weeksOfMonth = [];
        $this->monthsOfYear = [];
        $this->weeksOfYear = [];
    }

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getLastSent(): ?DateTime
    {
        if (0 == $this->broadcasts->count()) {
            return null;
        }
        if (1 == $this->broadcasts->count() && 0 === $this->broadcasts[0]->getEmailSentCount()) {
            return null;
        }

        $lastSent = $this->broadcasts[0]->getStarted();
        foreach ($this->broadcasts as $broadcast) {
            /** @var Broadcast $broadcast */
            if (0 === $broadcast->getEmailSentCount()) {
                // it was a test
                continue;
            }
            if ($lastSent->getTimestamp() < $broadcast->getStarted()->getTimestamp()) {
                $lastSent = $broadcast->getStarted();
            }
        }

        return $lastSent;
    }

    public function isRecurring(): bool
    {
        return $this->recurring;
    }

    public function setRecurring(bool $recurring): self
    {
        $this->recurring = $recurring;

        return $this;
    }

    public function getHoursOfDay(): array
    {
        return $this->hoursOfDay;
    }

    public function setHoursOfDay(array $hoursOfDay): self
    {
        $this->hoursOfDay = $hoursOfDay;

        return $this;
    }

    public function getDaysOfWeek(): array
    {
        return $this->daysOfWeek;
    }

    public function setDaysOfWeek(array $daysOfWeek): self
    {
        $this->daysOfWeek = $daysOfWeek;

        return $this;
    }

    public function getDaysOfMonth(): array
    {
        return $this->daysOfMonth;
    }

    public function setDaysOfMonth(array $daysOfMonth): self
    {
        $this->daysOfMonth = $daysOfMonth;

        return $this;
    }

    public function getDaysOfYear(): array
    {
        return $this->daysOfYear;
    }

    public function setDaysOfYear(array $daysOfYear): self
    {
        $this->daysOfYear = $daysOfYear;

        return $this;
    }

    public function getWeeksOfMonth(): array
    {
        return $this->weeksOfMonth;
    }

    public function setWeeksOfMonth(array $weeksOfMonth): self
    {
        $this->weeksOfMonth = $weeksOfMonth;

        return $this;
    }

    public function getMonthsOfYear(): array
    {
        return $this->monthsOfYear;
    }

    public function setMonthsOfYear(array $monthsOfYear): self
    {
        $this->monthsOfYear = $monthsOfYear;

        return $this;
    }

    public function getWeeksOfYear(): array
    {
        return $this->weeksOfYear;
    }

    public function setWeeksOfYear(array $weeksOfYear): self
    {
        $this->weeksOfYear = $weeksOfYear;

        return $this;
    }

    public function getCampaign(): Campaign
    {
        return $this->campaign;
    }

    public function setCampaign(?Campaign $campaign): self
    {
        $this->campaign = $campaign;

        return $this;
    }

    public function nextTick(): ?DateTime
    {
        try {
            $clock = new Clock(Carbon::now());

            return $clock->nextTick($this);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function hasBeenSent(): bool
    {
        return
            (false === $this->isRecurring() && self::SENT === $this->status) ||
            (true === $this->isRecurring() && null !== $this->getLastSent());
    }

    public function isPending(): bool
    {
        return self::PENDING === $this->status;
    }

    public function isDraft(): bool
    {
        return self::DRAFT === $this->status;
    }

    public function isArchived(): bool
    {
        return self::ARCHIVED === $this->status;
    }

    public function isAborted(): bool
    {
        return self::ABORTED === $this->status;
    }

    public function isProcessing(): bool
    {
        return self::PROCESSING === $this->status;
    }

    public function isTested(): bool
    {
        return self::TESTED === $this->status;
    }

    public function getBroadcasts()
    {
        return $this->broadcasts;
    }

    public function setBroadcasts(array $broadcasts): self
    {
        $this->broadcasts = $broadcasts;

        return $this;
    }

    public function addBroadcast(Broadcast $broadcast): self
    {
        if ($this->broadcasts->contains($broadcast)) {
            return $this;
        }
        $this->broadcasts->add($broadcast);
        $broadcast->setMailing($this);

        return $this;
    }

    public function getSiteAccess(): ?string
    {
        return $this->siteAccess;
    }

    public function setSiteAccess(string $siteAccess): self
    {
        $this->siteAccess = $siteAccess;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }
}

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
    const DRAFT = 0;

    /**
     * Ready to be sent.
     */
    const PENDING = 10;

    /**
     * Currently Processing.
     */
    const PROCESSING = 20;

    /**
     * Sent, Processing over.
     */
    const SENT = 30;

    /**
     * Aborted.
     */
    const ABORTED = 40;

    /**
     * Archived.
     */
    const ARCHIVED = 50;

    /**
     * Statuses.
     */
    const STATUSES = [
        self::DRAFT      => 'draft',
        self::PENDING    => 'pending',
        self::PROCESSING => 'processing',
        self::SENT       => 'sent',
        self::ABORTED    => 'aborted',
        self::ARCHIVED   => 'archived',
    ];

    /**
     * @var int
     * @ORM\Column(name="MAIL_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="MAIL_status", type="smallint", nullable=false)
     */
    private $status;

    /**
     * @var DateTime
     * @ORM\Column(name="MAIL_last_sent", type="datetime", nullable=true)
     */
    private $lastSent;

    /**
     * @var bool
     * @ORM\Column(name="MAIL_recurring", type="boolean", nullable=false)
     */
    private $recurring;

    /**
     * @var array
     * @ORM\Column(name="MAIL_hours_of_day", type="array", nullable=true)
     */
    private $hoursOfDay;

    /**
     * @var array
     * @ORM\Column(name="MAIL_days_of_week", type="array", nullable=true)
     */
    private $daysOfWeek;

    /**
     * @var array
     * @ORM\Column(name="MAIL_days_of_month", type="array", nullable=true)
     */
    private $daysOfMonth;

    /**
     * @var array
     * @ORM\Column(name="MAIL_weeks_of_month", type="array", nullable=true)
     */
    private $weeksOfMonth;

    /**
     * @var array
     * @ORM\Column(name="MAIL_months_of_year", type="array", nullable=true)
     */
    private $monthOfYear;

    /**
     * @var array
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
     * Mailing constructor.
     */
    public function __construct()
    {
        $this->recurring = false;
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
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return Mailing
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getLastSent(): DateTime
    {
        return $this->lastSent;
    }

    /**
     * @param DateTime $lastSent
     *
     * @return Mailing
     */
    public function setLastSent(DateTime $lastSent): self
    {
        $this->lastSent = $lastSent;

        return $this;
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
    public function getMonthOfYear(): array
    {
        return $this->monthOfYear;
    }

    /**
     * @param array $monthOfYear
     *
     * @return Mailing
     */
    public function setMonthOfYear(array $monthOfYear): self
    {
        $this->monthOfYear = $monthOfYear;

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
    public function setCampaign(Campaign $campaign): self
    {
        $this->campaign = $campaign;

        return $this;
    }
}

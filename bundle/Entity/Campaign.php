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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Campaign.
 *
 * @ORM\Table(name="novaezmailing_campaign")
 *
 * @ORM\Entity(repositoryClass="Novactive\Bundle\eZMailingBundle\Repository\Campaign")
 * @ORM\EntityListeners({"Novactive\Bundle\eZMailingBundle\Listener\EntityContentLink"})
 */
class Campaign implements eZ\ContentInterface
{
    use Compose\Metadata;
    use Compose\Names;
    use eZ\Content;

    /**
     * @var int
     * @ORM\Column(name="CAMP_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="CAMP_sender_name", type="string", length=255, nullable=false)
     */
    private $senderName;

    /**
     * @var string
     * @ORM\Column(name="CAMP_sender_email", type="string", length=255, nullable=false)
     */
    private $senderEmail;

    /**
     * @var string
     * @ORM\Column(name="CAMP_report_email", type="string", length=255, nullable=false)
     */
    private $reportEmail;

    /**
     * @var array
     * @ORM\Column(name="CAMP_siteaccess_limit", type="array", nullable=true)
     */
    private $siteaccessLimit;

    /**
     * @var MailingList[]
     * @ORM\ManyToMany(targetEntity="Novactive\Bundle\eZMailingBundle\Entity\MailingList")
     * @ORM\JoinTable(name="novaezmailing_campaign_mailinglists_destination",
     *      joinColumns={@ORM\JoinColumn(name="ML_id", referencedColumnName="CAMP_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="CAMP_id", referencedColumnName="ML_id")}
     *      )
     * @ORM\OrderBy({"created" = "ASC"})
     */
    private $mailingLists;

    /**
     * @var Mailing[]
     * @ORM\OneToMany(targetEntity="Novactive\Bundle\eZMailingBundle\Entity\Mailing", mappedBy="campaign",
     *                                                                                cascade={"persist","remove"})
     */
    private $mailings;

    /**
     * Campaign constructor.
     */
    public function __construct()
    {
        $this->mailingLists    = new ArrayCollection();
        $this->mailings        = new ArrayCollection();
        $this->siteaccessLimit = [];
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
     * @return Campaign
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getSenderName(): string
    {
        return $this->senderName;
    }

    /**
     * @param string $senderName
     *
     * @return Campaign
     */
    public function setSenderName(string $senderName): self
    {
        $this->senderName = $senderName;

        return $this;
    }

    /**
     * @return string
     */
    public function getSenderEmail(): string
    {
        return $this->senderEmail;
    }

    /**
     * @param string $senderEmail
     *
     * @return Campaign
     */
    public function setSenderEmail(string $senderEmail): self
    {
        $this->senderEmail = $senderEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getReportEmail(): string
    {
        return $this->reportEmail;
    }

    /**
     * @param string $reportEmail
     *
     * @return Campaign
     */
    public function setReportEmail(string $reportEmail): self
    {
        $this->reportEmail = $reportEmail;

        return $this;
    }

    /**
     * @return array
     */
    public function getSiteaccessLimit(): array
    {
        return $this->siteaccessLimit;
    }

    /**
     * @param array $siteaccessLimit
     *
     * @return Campaign
     */
    public function setSiteaccessLimit(array $siteaccessLimit): self
    {
        $this->siteaccessLimit = $siteaccessLimit;

        return $this;
    }

    /**
     * @return MailingList[]|ArrayCollection
     */
    public function getMailingLists()
    {
        return $this->mailingLists;
    }

    /**
     * @param MailingList[] $mailingLists
     *
     * @return Campaign
     */
    public function setMailingLists(array $mailingLists): self
    {
        foreach ($mailingLists as $mailingList) {
            if (!$mailingList instanceof MailingList) {
                throw new \RuntimeException(sprintf('Provided MailingList is not a %s', MailingList::class));
            }
        }
        $this->mailingLists = $mailingLists;

        return $this;
    }

    /**
     * @param MailingList $mailingList
     *
     * @return $this
     */
    public function addMailingList(MailingList $mailingList): self
    {
        if ($this->mailingLists->contains($mailingList)) {
            return $this;
        }

        $this->mailingLists->add($mailingList);

        return $this;
    }

    /**
     * @return ArrayCollection|Mailing[]
     */
    public function getMailings()
    {
        return $this->mailings;
    }

    /**
     * @param Mailing[] $mailings
     *
     * @return Campaign
     */
    public function setMailings(array $mailings): self
    {
        foreach ($mailings as $mailing) {
            if (!$mailing instanceof Mailing) {
                throw new \RuntimeException(sprintf('Provided MailingList is not a %s', Mailing::class));
            }
        }

        $this->mailings = $mailings;

        return $this;
    }

    /**
     * @param Mailing $mailing
     *
     * @return $this
     */
    public function addMailing(Mailing $mailing): self
    {
        if ($this->mailings->contains($mailing)) {
            return $this;
        }
        $this->mailings->add($mailing);
        $mailing->setCampaign($this);

        return $this;
    }
}

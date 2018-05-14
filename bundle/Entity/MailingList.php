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
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Generator;

/**
 * Class MailingList.
 *
 * @ORM\Table(name="novaezmailing_mailing_list")
 *
 * @ORM\Entity(repositoryClass="Novactive\Bundle\eZMailingBundle\Repository\MailingList")
 */
class MailingList
{
    use Compose\Remote;
    use Compose\Metadata;
    use Compose\Names;

    /**
     * @var int
     * @ORM\Column(name="ML_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Registration[]
     * @ORM\OrderBy({"created" = "ASC"})
     * @ORM\OneToMany(targetEntity="\Novactive\Bundle\eZMailingBundle\Entity\Registration", mappedBy="mailingList",
     *                                                                                      cascade={"persist","remove"},
     *                                                                                      orphanRemoval=true,
     *                                                                                      fetch="EXTRA_LAZY"
     * )
     */
    private $registrations;

    /**
     * @var array
     * @ORM\Column(name="ML_siteaccess_limit", type="array", nullable=true)
     */
    private $siteaccessLimit;

    /**
     * @var bool
     * @ORM\Column(name="ML_approbation", type="boolean", nullable=false)
     */
    private $withApproval;

    /**
     * @var Campaign[]
     * @ORM\ManyToMany(targetEntity="\Novactive\Bundle\eZMailingBundle\Entity\Campaign", mappedBy="mailingLists",
     *                                                                                  cascade={"persist"},
     *                                                                                  orphanRemoval=true,
     *                                                                                  fetch="EXTRA_LAZY")
     */
    private $campaigns;

    /**
     * MailingList constructor.
     */
    public function __construct()
    {
        $this->registrations = new ArrayCollection();
        $this->withApproval  = false;
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
     * @return MailingList
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return ArrayCollection|Registration[]
     */
    public function getRegistrations()
    {
        return $this->registrations;
    }

    /**
     * @return ArrayCollection|Registration[]
     */
    public function getApprovedRegistrations()
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('approved', true));

        return $this->registrations->matching($criteria);
    }

    /**
     * @param Registration[] $registrations
     *
     * @return MailingList
     */
    public function setRegistrations(array $registrations): self
    {
        foreach ($registrations as $registration) {
            if (!$registration instanceof Registration) {
                throw new \RuntimeException(sprintf('Provided Registration is not a %s', Registration::class));
            }
        }
        $this->registrations = $registrations;

        return $this;
    }

    /**
     * @return bool
     */
    public function isWithApproval(): bool
    {
        return $this->withApproval;
    }

    /**
     * @param bool $withApproval
     *
     * @return MailingList
     */
    public function setWithApproval(bool $withApproval): self
    {
        $this->withApproval = $withApproval;

        return $this;
    }

    /**
     * @return array
     */
    public function getSiteaccessLimit(): ?array
    {
        return $this->siteaccessLimit;
    }

    /**
     * @param array $siteaccessLimit
     *
     * @return MailingList
     */
    public function setSiteaccessLimit(array $siteaccessLimit): self
    {
        $this->siteaccessLimit = $siteaccessLimit;

        return $this;
    }

    /**
     * @return Campaign[]|ArrayCollection
     */
    public function getCampaigns()
    {
        return $this->campaigns;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName() ?? '';
    }

    /**
     * @return Generator
     */
    public function getValidRecipients(): Generator
    {
        foreach ($this->getApprovedRegistrations() as $registration) {
            $user = $registration->getUser();
            // send only to confirm and soft bounce
            if (!$user->isConfirmed() && !$user->isSoftBounce()) {
                continue;
            }

            yield $user;
        }
    }

    /**
     * @return int
     */
    public function getValidRecipientsCount(): int
    {
        return count(iterator_to_array($this->getValidRecipients(), false));
    }
}

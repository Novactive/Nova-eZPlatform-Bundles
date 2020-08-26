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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use RuntimeException;

/**
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

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
        $this->created = new DateTime();
        $this->withApproval = false;
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

    /**
     * @return ArrayCollection|Registration[]
     */
    public function getRegistrations()
    {
        return $this->registrations;
    }

    public function setRegistrations(array $registrations): self
    {
        foreach ($registrations as $registration) {
            if (!$registration instanceof Registration) {
                throw new RuntimeException(sprintf('Provided Registration is not a %s', Registration::class));
            }
        }
        $this->registrations = $registrations;

        return $this;
    }

    public function isWithApproval(): bool
    {
        return $this->withApproval;
    }

    public function setWithApproval(bool $withApproval): self
    {
        $this->withApproval = $withApproval;

        return $this;
    }

    public function getSiteaccessLimit(): ?array
    {
        return $this->siteaccessLimit;
    }

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

    public function __toString(): string
    {
        return $this->getName() ?? '';
    }
}

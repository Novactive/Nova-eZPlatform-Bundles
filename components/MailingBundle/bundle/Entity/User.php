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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="novaezmailing_user",
 *            uniqueConstraints={ @ORM\UniqueConstraint(name="unique_email",columns={"USER_email"})},
 *            indexes={
 *
 *                @ORM\Index(name="search_idx_restricted", columns={"USER_restricted"}),
 *                @ORM\Index(name="search_idx_status", columns={"USER_status"})
 *            }
 * )
 *
 * @ORM\Entity(repositoryClass="Novactive\Bundle\eZMailingBundle\Repository\User")
 *
 * @UniqueEntity(
 *     fields={"email"},
 *     errorPath="email",
 *     message="This email {{ value }} is already in use"
 * )
 */
class User
{
    use Compose\Metadata;
    use Compose\Remote;

    /**
     * In the BD, weird fallback status (should not exist).
     */
    public const IN_BASE = 0;

    /**
     * Did not confirmed the confirmation email.
     */
    public const PENDING = 'pending';

    /**
     * Did confirme the confirmation email.
     */
    public const CONFIRMED = 'confirmed';

    /**
     * Flag as SOFT BOUNCE.
     */
    public const SOFT_BOUNCE = 'soft_bounce';

    /**
     * Flag as HARD BOUNCE.
     */
    public const HARD_BOUNCE = 'hard_bounce';

    /**
     * Was blacklisted.
     */
    public const BLACKLISTED = 'blacklisted';

    /**
     * Statuses.
     */
    public const STATUSES = [
        self::PENDING,
        self::CONFIRMED,
        self::SOFT_BOUNCE,
        self::HARD_BOUNCE,
        self::BLACKLISTED,
    ];

    /**
     * Styles.
     */
    public const STATUSES_STYLE = [
        self::PENDING => 'dark',
        self::CONFIRMED => 'success',
        self::SOFT_BOUNCE => 'warning',
        self::HARD_BOUNCE => 'danger',
        self::BLACKLISTED => 'info',
    ];

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
        $this->created = new \DateTime();
        $this->restricted = false;
        $this->updated = new \DateTime();
    }

    /**
     * @var int
     *
     * @ORM\Column(name="USER_id", type="bigint", nullable=false)
     *
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="USER_email", type="string", length=255, nullable=false)
     *
     * @Assert\NotBlank(message="Email is mandatory")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="USER_first_name", type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="USER_last_name", type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="USER_gender", type="string", length=255, nullable=true)
     */
    private $gender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="USER_birth_date", type="date", nullable=true)
     */
    private $birthDate;

    /**
     * @var string
     *
     * @ORM\Column(name="USER_phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="USER_zipcode", type="string", length=255, nullable=true)
     */
    private $zipcode;

    /**
     * @var string
     *
     * @ORM\Column(name="USER_city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="USER_state", type="string", length=255, nullable=true)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="USER_country", type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="USER_job_title", type="string", length=255, nullable=true)
     */
    private $jobTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="USER_company", type="string", length=255, nullable=true)
     */
    private $company;

    /**
     * @var string
     *
     * @ORM\Column(name="USER_origin", type="string", length=255, nullable=false)
     */
    private $origin;

    /**
     * @var string
     *
     * @ORM\Column(name="USER_status", type="string", nullable=false)
     */
    private $status;

    /**
     * @var bool
     *
     * @ORM\Column(name="USER_restricted", type="boolean", nullable=false)
     */
    private $restricted;

    /**
     * @var Registration[]
     *
     * @ORM\OrderBy({"created" = "ASC"})
     *
     * @ORM\OneToMany(targetEntity="Novactive\Bundle\eZMailingBundle\Entity\Registration", mappedBy="user",
     *                                                                                     cascade={"persist","remove"},
     *                                                                                     orphanRemoval=true
     * )
     */
    private $registrations;

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getBirthDate(): ?\DateTime
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTime $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(string $jobTitle): self
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getOrigin(): string
    {
        return $this->origin;
    }

    public function setOrigin(string $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getStatusStyle(): string
    {
        return self::STATUSES_STYLE[$this->status];
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function isRestricted(): bool
    {
        return $this->restricted;
    }

    public function setRestricted(bool $restricted): self
    {
        $this->restricted = $restricted;

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
    public function getPendingRegistrations()
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
            $this->addRegistration($registration);
        }

        return $this;
    }

    public function addRegistration(Registration $registration): self
    {
        if ($this->registrations->contains($registration)) {
            return $this;
        }

        if (
            $this->registrations->exists(
                function ($key, Registration $element) use ($registration) {
                    // tricks phpmd

                    return $element->getMailingList()->getId() === $registration->getMailingList()->getId();
                }
            )
        ) {
            return $this;
        }
        $registration->setUser($this);
        $this->registrations->add($registration);

        return $this;
    }

    public function removeRegistration(Registration $registration): self
    {
        $this->registrations->removeElement($registration);

        return $this;
    }

    public function isConfirmed(): bool
    {
        return self::CONFIRMED === $this->status;
    }

    public function isPending(): bool
    {
        return self::PENDING === $this->status;
    }

    public function isBlacklisted(): bool
    {
        return self::BLACKLISTED === $this->status;
    }

    public function isSoftBounce(): bool
    {
        return self::SOFT_BOUNCE === $this->status;
    }

    public function isHardBounce(): bool
    {
        return self::HARD_BOUNCE === $this->status;
    }
}

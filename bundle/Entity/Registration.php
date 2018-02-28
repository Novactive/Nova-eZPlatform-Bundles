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

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Registration.
 *
 * @ORM\Table(name="novaezmailing_registrations",
 *            uniqueConstraints={ @ORM\UniqueConstraint(name="unique_registration",columns={"ML_id","USER_id"})}
 * )
 *
 * @ORM\Entity(repositoryClass="Novactive\Bundle\eZMailingBundle\Repository\Registration")
 */
class Registration
{
    use Compose\Metadata;

    /**
     * @var int
     * @ORM\Column(name="REG_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var MailingList
     * @ORM\ManyToOne(targetEntity="Novactive\Bundle\eZMailingBundle\Entity\MailingList", inversedBy="registrations")
     * @ORM\JoinColumn(name="ML_id", referencedColumnName="ML_id", nullable=false)
     */
    private $mailingList;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Novactive\Bundle\eZMailingBundle\Entity\User", inversedBy="registrations")
     * @ORM\JoinColumn(name="USER_id", referencedColumnName="USER_id", nullable=false)
     */
    private $user;

    /**
     * @var bool
     * @ORM\Column(name="REG_approved", type="boolean", nullable=false)
     */
    private $approved;

    /**
     * Registration constructor.
     */
    public function __construct()
    {
        $this->approved = false;
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
     * @return Registration
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMailingList(): MailingList
    {
        return $this->mailingList;
    }

    /**
     * @param MailingList $mailingList
     *
     * @return Registration
     */
    public function setMailingList($mailingList): self
    {
        $this->mailingList = $mailingList;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return Registration
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->approved;
    }

    /**
     * @param bool $approved
     *
     * @return Registration
     */
    public function setApproved(bool $approved): self
    {
        $this->approved = $approved;

        return $this;
    }
}

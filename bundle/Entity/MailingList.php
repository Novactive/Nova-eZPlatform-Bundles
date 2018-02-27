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
     * MailingList constructor.
     */
    public function __construct()
    {
        $this->registrations = new ArrayCollection();
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
}

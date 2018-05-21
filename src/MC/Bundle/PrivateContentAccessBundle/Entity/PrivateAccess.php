<?php

namespace MC\Bundle\PrivateContentAccessBundle\Entity;

use Doctrine\DBAL\Types\BooleanType;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="PrivateAccessRepository")
 * @UniqueEntity(fields="location_path", message="Location already taken")
 */
class PrivateAccess
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", unique=true)
     * @Assert\NotBlank()
     */
    protected $locationId;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank()
     */
    protected $password;

    /**
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    protected $activate = 0;


    public function __get($name)
    {
        // TODO: Implement __get() method.
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return integer
     */
    public function getLocationId(): integer
    {
        return $this->locationId;
    }

    /**
     * @param integer $location_id
     */
    public function setLocationId(integer $location_id): void
    {
        $this->locationId = $location_id;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $password
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return \DateTime
     */
    /*public function getCreated(): DateTime
    {
        return $this->created;
    }*/

    /**
     * @param \DateTime $created
     */
    public function setCreated(\DateTime $created): void
    {
        $this->created = $created;
    }

    /**
     * @return bool
     */
    public function getActivate(): bool
    {
        return boolval($this->activate);
    }

    /**
     * @param bool $activate
     */
    public function setActivate($activate): void
    {
        $this->activate = boolval($activate);
    }
}

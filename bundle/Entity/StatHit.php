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
 * Class StatHit.
 *
 * @ORM\Table(name="novaezmailing_stats_hit")
 *
 * @ORM\Entity(repositoryClass="Novactive\Bundle\eZMailingBundle\Repository\StatHit")
 */
class StatHit
{
    use Compose\Metadata;

    /**
     * @var int
     *
     * @ORM\Column(name="STHIT_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="STHIT_url", type="string", nullable=false)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="STHIT_user_key", type="string", nullable=false)
     */
    private $userKey;

    /**
     * @var string
     *
     * @ORM\Column(name="STHIT_os_name", type="string", nullable=true)
     */
    private $osName;

    /**
     * @var string
     *
     * @ORM\Column(name="STHIT_browser_name", type="string", nullable=true)
     */
    private $browserName;

    /**
     * @var Broadcast
     * @ORM\ManyToOne(targetEntity="Novactive\Bundle\eZMailingBundle\Entity\Broadcast")
     * @ORM\JoinColumn(name="BDCST_id", referencedColumnName="BDCST_id")
     */
    private $broadcast;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserKey(): string
    {
        return $this->userKey;
    }

    /**
     * @param string $userKey
     *
     * @return $this
     */
    public function setUserKey(string $userKey): self
    {
        $this->userKey = $userKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getOsName(): string
    {
        return $this->osName;
    }

    /**
     * @param string $osName
     *
     * @return $this
     */
    public function setOsName(string $osName): self
    {
        $this->osName = $osName;

        return $this;
    }

    /**
     * @return string
     */
    public function getBrowserName(): string
    {
        return $this->browserName;
    }

    /**
     * @param string $browserName
     *
     * @return $this
     */
    public function setBrowserName(string $browserName): self
    {
        $this->browserName = $browserName;

        return $this;
    }

    /**
     * @return Broadcast
     */
    public function getBroadcast(): Broadcast
    {
        return $this->broadcast;
    }

    /**
     * @param Broadcast $broadcast
     *
     * @return $this
     */
    public function setBroadcast(Broadcast $broadcast): self
    {
        $this->broadcast = $broadcast;

        return $this;
    }
}

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
 * Class ConfirmationToken.
 *
 * @ORM\Table(name="novaezmailing_confirmation_token")
 *
 * @ORM\Entity(repositoryClass="Novactive\Bundle\eZMailingBundle\Repository\ConfirmationToken")
 */
class ConfirmationToken
{
    use Compose\Metadata;

    public const REGISTER = 'register';

    public const UNREGISTER = 'unregister';

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(name="CT_id", type="guid", unique=true)
     */
    private $id;

    /**
     * @var array
     * @ORM\Column(name="CT_payload", type="array", nullable=false)
     */
    private $payload;

    /**
     * ConfirmationToken constructor.
     */
    public function __construct()
    {
        $this->created = new DateTime();
        $this->updated = new DateTime();
    }

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @return $this
     */
    public function setPayload(array $payload): self
    {
        $this->payload = $payload;

        return $this;
    }
}

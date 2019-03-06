<?php
/**
 * NovaeZProtectedContentBundle.
 *
 * @package   Novactive\Bundle\eZProtectedContentBundle
 *
 * @author    Novactive
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/eZProtectedContentBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZProtectedContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Novactive\Bundle\eZProtectedContentBundle\Entity\eZ\ContentInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Novactive\Bundle\eZProtectedContentBundle\Repository\ProtectedAccessRepository")
 * @ORM\Table(name="novaezprotectedcontent")
 * @ORM\EntityListeners({"Novactive\Bundle\eZProtectedContentBundle\Listener\EntityContentLink"})
 */
class ProtectedAccess implements ContentInterface
{
    use Compose\Metadata;
    use eZ\Content;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    protected $password;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $enabled;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $protectChildren;

    public function __construct()
    {
        $this->enabled         = true;
        $this->protectChildren = true;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password ?? '';
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function isProtectChildren(): bool
    {
        return $this->protectChildren;
    }

    public function setProtectChildren(bool $protectChildren): void
    {
        $this->protectChildren = $protectChildren;
    }
}

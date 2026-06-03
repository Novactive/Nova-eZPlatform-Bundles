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

#[ORM\Entity]
#[ORM\Table(name: 'novaezprotectedcontent')]
class ProtectedAccess implements ContentInterface
{
    use Compose\Metadata;
    use eZ\Content;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $password;

    #[ORM\Column(type: 'boolean', nullable: false)]
    protected bool $enabled;

    #[ORM\Column(name: 'as_email', type: 'boolean', nullable: false)]
    protected bool $asEmail = false;

    #[ORM\Column(name: 'protect_children', type: 'boolean', nullable: false)]
    protected bool $protectChildren;

    #[ORM\Column(name: 'email_message', type: 'string', nullable: true)]
    protected string $emailMessage;

    public function __construct()
    {
        $this->enabled = true;
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

    public function getAsEmail(): bool
    {
        return $this->asEmail ?? false;
    }

    public function setAsEmail(bool $asEmail): self
    {
        $this->asEmail = $asEmail;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password ?? '';
    }

    public function setPassword(?string $password): self
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

    public function getEmailMessage(): ?string
    {
        return $this->emailMessage;
    }

    public function setEmailMessage(string $emailMessage): void
    {
        $this->emailMessage = $emailMessage;
    }

    public function getContentId(): int
    {
        return $this->contentId;
    }
}

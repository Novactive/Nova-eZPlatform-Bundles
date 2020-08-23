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

namespace Novactive\Bundle\eZMailingBundle\Entity\Compose;

use Symfony\Component\Validator\Constraints as Assert;

trait Names
{
    /**
     * @var array
     * @Assert\NotBlank()
     * @ORM\Column(name="OBJ_names", type="array", nullable=false)
     */
    private $names;

    /**
     * @return array
     */
    public function getNames(): ?array
    {
        return $this->names;
    }

    public function setNames(array $names): self
    {
        $this->names = $names;

        return $this;
    }

    public function getName(?string $lang = null): ?string
    {
        if (null === $this->names) {
            return null;
        }
        if (null === $lang || !isset($this->names[$lang])) {
            return array_values($this->names)[0];
        }

        return $this->names[$lang];
    }
}

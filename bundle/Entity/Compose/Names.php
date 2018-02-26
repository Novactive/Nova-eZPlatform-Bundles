<?php
/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/eZMailingBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Entity\Compose;

/**
 * Trait Names.
 */
trait Names
{
    /**
     * @var array
     * @ORM\Column(name="OBJ_names", type="array", nullable=false)
     */
    private $names;

    /**
     * @return array
     */
    public function getNames(): array
    {
        return $this->names;
    }

    /**
     * @param array $names
     *
     * @return Names
     */
    public function setNames(array $names): self
    {
        $this->names = $names;

        return $this;
    }

    /**
     * @param null|string $lang
     *
     * @return string
     */
    public function getName(?string $lang = null): string
    {
        if (null === $lang || !isset($this->names[$lang])) {
            return array_values($this->names)[0];
        }

        return $this->names[$lang];
    }
}

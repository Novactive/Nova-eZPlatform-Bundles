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

namespace Novactive\Bundle\eZMailingBundle\Entity\JSON;

/**
 * Class Email.
 */
class Email
{
    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $name;

    /**
     * Email constructor.
     *
     * @param string|null $email
     * @param null|string $name
     */
    public function __construct(string $email = null, ?string $name = null)
    {
        $this->email = $email;
        $this->name  = $name;
    }
}

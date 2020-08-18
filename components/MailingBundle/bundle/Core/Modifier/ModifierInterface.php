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

namespace Novactive\Bundle\eZMailingBundle\Core\Modifier;

use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use Novactive\Bundle\eZMailingBundle\Entity\User;

/**
 * Interface ModifierInterface.
 */
interface ModifierInterface
{
    /**
     * @param Mailing $mailing
     * @param User    $user
     * @param string  $html
     * @param array   $options
     *
     * @return string
     */
    public function modify(Mailing $mailing, User $user, string $html, array $options = []): string;
}

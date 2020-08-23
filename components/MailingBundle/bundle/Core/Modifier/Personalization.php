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

class Personalization
{
    public function modify(Mailing $mailing, User $user, string $html, array $options = []): string
    {
        $map = [
            '##EMAIL##' => $user->getEmail(),
            '##FIRSTNAME##' => $user->getFirstName(),
            '##LASTNAME##' => $user->getLastName(),
            '##COUNTRY##' => $user->getCountry(),
            '##CITY##' => $user->getCity(),
            '##COMPANY##' => $user->getCompany(),
            '##GENDER##' => $user->getGender(),
            '##JOBTITLE##' => $user->getJobTitle(),
            '##PHONE##' => $user->getPhone(),
            '##ZIPCODE##' => $user->getZipcode(),
        ];

        return str_replace(array_keys($map), array_values($map), $html);
    }
}

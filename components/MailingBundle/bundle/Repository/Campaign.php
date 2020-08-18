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

namespace Novactive\Bundle\eZMailingBundle\Repository;

/**
 * Class Campaign.
 */
class Campaign extends EntityRepository
{
    /**
     * {@inheritdoc}
     */
    protected function getAlias(): string
    {
        return 'camp';
    }
}

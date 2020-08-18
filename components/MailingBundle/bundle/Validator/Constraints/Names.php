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

namespace Novactive\Bundle\eZMailingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Names.
 *
 * @Annotation
 */
class Names extends Constraint
{
    public $message = 'The Name should be NOT empty.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return NamesValidator::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

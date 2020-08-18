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
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class NamesValidator.
 */
class NamesValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($values, Constraint $constraint): void
    {
        $empty = true;
        foreach ($values as $value) {
            if (null !== $value) {
                $empty = false;
                break;
            }
        }
        if ($empty) {
            $this->context->buildViolation($constraint->message)
                          ->atPath('names')
                          ->addViolation();
        }
    }
}

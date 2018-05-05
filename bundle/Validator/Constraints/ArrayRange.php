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
 * Class ArrayRange.
 *
 * @Annotation
 */
class ArrayRange extends Constraint
{
    /**
     * @var string
     */
    public $message = 'The value "{{ value }}" is invalid or out of range {{ min }} {{ max }}.';

    /**
     * @var int
     */
    public $min;

    /**
     * @var int
     */
    public $max;

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return ArrayRangeValidator::class;
    }
}

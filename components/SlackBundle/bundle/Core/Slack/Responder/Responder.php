<?php

/**
 * NovaeZSlackBundle Bundle.
 *
 * @package   Novactive\Bundle\eZSlackBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZSlackBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZSlackBundle\Core\Slack\Responder;

abstract class Responder implements ResponderInterface
{
    /**
     * Get The Name based on the Class Name.
     */
    final public function getName(): string
    {
        return static::getNameForClassName(static::class);
    }

    /**
     * Get the Name for a class.
     */
    final public static function getNameForClassName(string $className): string
    {
        $path = explode('\\', $className);

        return strtolower(array_pop($path));
    }
}

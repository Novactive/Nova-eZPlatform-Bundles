<?php

/**
 * Nova eZ Algolia Search Engine.
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @licence   "SEE FULL LICENSE OPTIONS IN LICENSE.md"
 *            Nova eZ Algolia Search Engine is tri-licensed, meaning you must choose one of three licenses to use:
 *                - Commercial License: a paid license, meant for commercial use. The default option for most users.
 *                - Creative Commons Non-Commercial No-Derivatives: meant for trial and non-commercial use.
 *                - GPLv3 License: meant for open-source projects.
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\CriterionVisitor\Contracts;

trait Helper
{
    private function toString($value): string
    {
        switch (\gettype($value)) {
            case 'boolean':
                return $value ? 'true' : 'false';
            case 'double':
                return sprintf('%F', $value);

            default:
                return (string) $value;
        }
    }

    private function escapeQuote($string, $doubleQuote = false)
    {
        $pattern = ($doubleQuote ? '/("|\\\)/' : '/(\'|\\\)/');

        return preg_replace($pattern, '\\\$1', $string);
    }
}

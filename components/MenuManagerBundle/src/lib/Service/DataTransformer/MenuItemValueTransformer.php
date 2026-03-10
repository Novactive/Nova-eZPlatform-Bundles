<?php

/**
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Novactive\EzMenuManager\Service\DataTransformer;

use Novactive\EzMenuManager\FieldType\MenuItem\ValueConverter;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class MenuItemValueTransformer.
 *
 * @package Novactive\EzMenuManager\Service
 */
class MenuItemValueTransformer implements DataTransformerInterface
{
    /** @var ValueConverter */
    protected $valueConverter;

    /**
     * FieldValueTransformer constructor.
     */
    public function __construct(ValueConverter $valueConverter)
    {
        $this->valueConverter = $valueConverter;
    }

    public function transform($value): mixed
    {
        return json_encode($this->valueConverter->toHash($value));
    }

    public function reverseTransform($value): mixed
    {
        $hash = json_decode((string) $value, true);

        return $this->valueConverter->fromHash($hash);
    }
}

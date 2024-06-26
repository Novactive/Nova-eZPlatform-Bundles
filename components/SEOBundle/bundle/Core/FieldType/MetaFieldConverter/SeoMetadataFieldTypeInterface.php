<?php

/**
 * NovaeZSEOBundle SeoMetadataFieldTypeInterface.
 *
 * @package   Novactive\Bundle\eZSEOBundle
 *
 * @author    Novactive <novaseobundle@novactive.com>
 * @copyright 2021 Novactive
 * @license   https://github.com/Novactive/NovaeZSEOBundle/blob/master/LICENSE MIT Licence
 */

namespace Novactive\Bundle\eZSEOBundle\Core\FieldType\MetaFieldConverter;

use Novactive\Bundle\eZSEOBundle\Core\Meta;
use Symfony\Component\Form\FormBuilderInterface;

interface SeoMetadataFieldTypeInterface
{
    /**
     * @param $hash
     */
    public function fromHash($hash): Meta;

    public function support(string $fieldType): bool;

    public function mapForm(FormBuilderInterface &$builder, array $params);
}

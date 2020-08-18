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

namespace Novactive\EzMenuManager\Migration\FieldHandler;

use Kaliop\eZMigrationBundle\API\FieldValueImporterInterface;
use Kaliop\eZMigrationBundle\Core\FieldHandler\AbstractFieldHandler;
use Novactive\EzMenuManager\FieldType\MenuItem\ValueConverter;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;

class MenuItemFieldHandler extends AbstractFieldHandler implements FieldValueImporterInterface
{
    /** @var ValueConverter */
    protected $valueConverter;

    /**
     * MenuItemFieldHandler constructor.
     */
    public function __construct(ValueConverter $valueConverter)
    {
        $this->valueConverter = $valueConverter;
    }

    /**
     * @inheritDoc
     */
    public function hashToFieldValue($fieldHash, array $context = [])
    {
        foreach ($fieldHash as &$fieldHashItem) {
            /** @var MenuItem[] $parentMenuItem */
            $parentMenuItem = $this->referenceResolver->resolveReference($fieldHashItem['parentId']);
            if (is_array($parentMenuItem)) {
                $fieldHashItem['parentId'] = null;
                foreach ($parentMenuItem as $parentMenuItem) {
                    if ($parentMenuItem->getMenu()->getId() === $fieldHashItem['menuId']) {
                        $fieldHashItem['parentId'] = $parentMenuItem->getId();
                        break;
                    }
                }
            }
        }

        return $this->valueConverter->fromHash($fieldHash);
    }
}

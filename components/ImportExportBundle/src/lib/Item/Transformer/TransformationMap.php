<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer;

use AlmaviaCX\Bundle\IbexaImportExport\Reference\Reference;

/**
 * @phpstan-type ElementSourceSingle string|Source|Reference
 * @phpstan-type ElementSourceArray array<int|string, ElementSourceSingle>
 * @phpstan-type ElementSource ElementSourceSingle|ElementSourceArray
 * @extends AbstractTransformationMap<ElementSource>
 */
class TransformationMap extends AbstractTransformationMap
{
}

<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer;

use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\TransformationMap;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Reference\ReferenceMap;

/**
 * @property TransformationMap $map
 * @property ReferenceMap      $referencesMap
 */
class WriterOptions extends ProcessorOptions
{
    protected TransformationMap $map;
    protected ?ReferenceMap $referencesMap = null;
}

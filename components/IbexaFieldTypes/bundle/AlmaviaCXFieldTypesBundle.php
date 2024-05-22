<?php

declare(strict_types=1);

namespace AlmaviaCX\Ibexa\Bundle\FieldTypes;

use AlmaviaCX\Ibexa\Bundle\FieldTypes\DependencyInjection\AlmaviaCXFieldTypesExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AlmaviaCXFieldTypesBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new AlmaviaCXFieldTypesExtension();
        }

        return $this->extension;
    }
}

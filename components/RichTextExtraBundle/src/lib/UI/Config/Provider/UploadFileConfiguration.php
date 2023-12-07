<?php

namespace AlmaviaCX\Bundle\IbexaRichTextExtra\UI\Config\Provider;

use AlmaviaCX\Bundle\IbexaRichTextExtra\FieldType\BinaryFile\Mapper;
use Ibexa\Contracts\AdminUi\UI\Config\ProviderInterface;

class UploadFileConfiguration implements ProviderInterface
{
    protected Mapper $mapper;

    public function __construct(Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getConfig()
    {
        return $this->mapper->getMappings();
    }
}

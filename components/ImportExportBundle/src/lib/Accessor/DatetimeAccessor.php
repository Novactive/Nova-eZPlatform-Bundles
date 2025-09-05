<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor;

class DatetimeAccessor extends AbstractItemAccessor
{
    public int $timestamp;
    public string $ISO8601;
    public string $YMD;

    public function __construct(\DateTime $dateTime)
    {
        $this->timestamp = $dateTime->getTimestamp();
        $this->ISO8601 = $dateTime->format('c');
        $this->YMD = $dateTime->format('Y-m-d');
    }
}

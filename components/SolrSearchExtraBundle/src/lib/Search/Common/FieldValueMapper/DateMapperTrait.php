<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Search\Common\FieldValueMapper;

use DateTime;
use Exception;
use InvalidArgumentException;

trait DateMapperTrait
{
    protected function mapDate($value): string
    {
        if (is_numeric($value)) {
            $date = new DateTime("@{$value}");
        } else {
            try {
                $date = new DateTime($value);
            } catch (Exception $e) {
                throw new InvalidArgumentException('Invalid date provided: '.$value);
            }
        }

        return $date->format('Y-m-d\\TH:i:s\\Z');
    }

    protected function mapDateRange($dateFrom, $dateTo): string
    {
        return sprintf(
            '[%s TO %s]',
            $this->mapDate($dateFrom),
            $this->mapDate($dateTo)
        );
    }
}

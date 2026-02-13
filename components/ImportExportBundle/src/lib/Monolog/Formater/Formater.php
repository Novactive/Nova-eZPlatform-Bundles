<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Monolog\Formater;

use Monolog\Formatter\HtmlFormatter;
use Monolog\Logger;

class Formater extends HtmlFormatter
{
    protected $logLevels = [
        Logger::DEBUG => 'bg-info',
        Logger::INFO => 'bg-success text-white',
        Logger::NOTICE => 'bg-light text-white',
        Logger::WARNING => 'bg-warning',
        Logger::ERROR => 'bg-danger text-white',
        Logger::CRITICAL => 'bg-danger text-white',
        Logger::ALERT => 'bg-danger text-white',
        Logger::EMERGENCY => 'bg-dark text-white',
    ];

    public function format(array $record): string
    {
        $title = sprintf(
            '[%s] %s',
            $record['level_name'],
            (string) $record['message'],
        );
        if (isset($record['context']['item_index'])) {
            $title = sprintf(
                '[Item %s]%s',
                $record['context']['item_index'],
                $title,
            );
            unset($record['context']['item_index']);
        }
        $output = $this->addTitle(
            $title,
            $record['level']
        );
        $output .= '<table cellspacing="1" width="100%" class="monolog-output">';

        if ($record['context']) {
            $embeddedTable = '<table cellspacing="1" width="100%">';
            foreach ($record['context'] as $key => $value) {
                $embeddedTable .= $this->addRow((string) $key, $this->convertToString($value));
            }
            $embeddedTable .= '</table>';
            $output .= $this->addRow('Context', $embeddedTable, false);
        }
        if ($record['extra']) {
            $embeddedTable = '<table cellspacing="1" width="100%">';
            foreach ($record['extra'] as $key => $value) {
                $embeddedTable .= $this->addRow((string) $key, $this->convertToString($value));
            }
            $embeddedTable .= '</table>';
            $output .= $this->addRow('Extra', $embeddedTable, false);
        }

        return $output.'</table>';
    }

    /**
     * Create a HTML h1 tag.
     *
     * @param string $title Text to be in the h1
     * @param int    $level Error level
     */
    protected function addTitle(string $title, int $level): string
    {
        $title = htmlspecialchars($title, ENT_NOQUOTES, 'UTF-8');

        return '<div class="p-3 mb-2 '.$this->logLevels[$level].'">'.$title.'</div>';
    }
}

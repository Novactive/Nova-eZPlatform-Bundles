<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Monolog\Handler;

use AlmaviaCX\Bundle\IbexaImportExport\Job\JobRecord;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Symfony\Component\Uid\Ulid;

/**
 * @phpstan-import-type Record from \Monolog\Logger
 * @phpstan-import-type Level from \Monolog\Logger
 * @phpstan-import-type LevelName from \Monolog\Logger
 */
class WorkflowHandler extends AbstractProcessingHandler
{
    /** @var array<JobRecord> */
    protected array $records = [];
    /** @var array<Level, JobRecord[]> */
    protected array $recordsByLevel = [];

    protected bool $skipReset = false;

    public function __construct($level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
    }

    /**
     * @return array<JobRecord>
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @return void
     */
    public function clear()
    {
        $this->records = [];
        $this->recordsByLevel = [];
    }

    /**
     * @return void
     */
    public function reset()
    {
        if (!$this->skipReset) {
            $this->clear();
        }
    }

    /**
     * @return void
     */
    public function setSkipReset(bool $skipReset)
    {
        $this->skipReset = $skipReset;
    }

    /**
     * @param string|int $level Logging level value or name
     *
     * @phpstan-param Level|LevelName|LogLevel::* $level
     */
    public function hasRecords($level): bool
    {
        return isset($this->recordsByLevel[Logger::toMonologLevel($level)]);
    }

    /**
     * @param string|array $record Either a message string or an array containing message
     *                             and optionally context keys that will be checked against all records
     * @param string|int   $level  Logging level value or name
     *
     * @phpstan-param array{message: string, context?: mixed[]}|string $record
     * @phpstan-param Level|LevelName|LogLevel::*                      $level
     */
    public function hasRecord($record, $level): bool
    {
        if (is_string($record)) {
            $record = ['message' => $record];
        }

        return $this->hasRecordThatPasses(function ($rec) use ($record) {
            if ($rec['message'] !== $record['message']) {
                return false;
            }
            if (isset($record['context']) && $rec['context'] !== $record['context']) {
                return false;
            }

            return true;
        }, $level);
    }

    /**
     * @param string|int $level Logging level value or name
     *
     * @phpstan-param Level|LevelName|LogLevel::* $level
     */
    public function hasRecordThatContains(string $message, $level): bool
    {
        return $this->hasRecordThatPasses(function ($rec) use ($message) {
            return false !== strpos($rec['message'], $message);
        }, $level);
    }

    /**
     * @param string|int $level Logging level value or name
     *
     * @phpstan-param Level|LevelName|LogLevel::* $level
     */
    public function hasRecordThatMatches(string $regex, $level): bool
    {
        return $this->hasRecordThatPasses(function (array $rec) use ($regex): bool {
            return preg_match($regex, $rec['message']) > 0;
        }, $level);
    }

    /**
     * @param string|int $level Logging level value or name
     *
     * @return bool
     *
     * @psalm-param callable(Record, int): mixed $predicate
     *
     * @phpstan-param Level|LevelName|LogLevel::* $level
     */
    public function hasRecordThatPasses(callable $predicate, $level)
    {
        $level = Logger::toMonologLevel($level);

        if (!isset($this->recordsByLevel[$level])) {
            return false;
        }

        foreach ($this->recordsByLevel[$level] as $i => $rec) {
            if ($predicate($rec->getRecord(), $i)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record): void
    {
        $jobRecord = new JobRecord(
            new Ulid(),
            $record
        );
        $this->recordsByLevel[$record['level']][] = $jobRecord;
        $this->records[] = $jobRecord;
    }

    /**
     * @param mixed[] $args
     *
     * @return bool
     */
    public function __call(string $method, array $args)
    {
        if (preg_match('/(.*)(Debug|Info|Notice|Warning|Error|Critical|Alert|Emergency)(.*)/', $method, $matches) > 0) {
            $genericMethod = $matches[1].('Records' !== $matches[3] ? 'Record' : '').$matches[3];
            $level = constant('Monolog\Logger::'.strtoupper($matches[2]));
            $callback = [$this, $genericMethod];
            if (is_callable($callback)) {
                $args[] = $level;

                return call_user_func_array($callback, $args);
            }
        }

        throw new \BadMethodCallException('Call to undefined method '.get_class($this).'::'.$method.'()');
    }
}

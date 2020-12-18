<?php

/**
 * Nova eZ Algolia Search Engine.
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @licence   "SEE FULL LICENSE OPTIONS IN LICENSE.md"
 *            Nova eZ Algolia Search Engine is tri-licensed, meaning you must choose one of three licenses to use:
 *                - Commercial License: a paid license, meant for commercial use. The default option for most users.
 *                - Creative Commons Non-Commercial No-Derivatives: meant for trial and non-commercial use.
 *                - GPLv3 License: meant for open-source projects.
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Core\DataCollector;

class Logger
{
    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var array
     */
    private $logs;

    public function __construct(bool $enabled = false)
    {
        $this->enabled = $enabled;
        $this->logs = [];
    }

    private function isEnabled(): bool
    {
        return true === $this->enabled;
    }

    public function addSearch(
        string $mode,
        string $languageCode,
        string $replicaName,
        string $query,
        array $requestOptions,
        array $results
    ): self {
        if (!$this->isEnabled()) {
            return $this;
        }

        $log = new Log();
        $log->fromSearch($mode, $languageCode, $replicaName, $query, $requestOptions, $results);
        $this->logs[] = $log;

        return $this;
    }

    public function addSave(string $mode, string $languageCode, string $replicaName, array $objects): self
    {
        if (!$this->isEnabled()) {
            return $this;
        }

        $log = new Log();
        $log->fromSave($mode, $languageCode, $replicaName, $objects);
        $this->logs[] = $log;

        return $this;
    }

    public function addDelete(string $mode, string $languageCode, string $replicaName, array $objects): self
    {
        if (!$this->isEnabled()) {
            return $this;
        }
        $log = new Log();
        $log->fromDelete($mode, $languageCode, $replicaName, $objects);
        $this->logs[] = $log;

        return $this;
    }

    public function addPurge(string $mode, string $replicaName): self
    {
        if (!$this->isEnabled()) {
            return $this;
        }
        $log = new Log();
        $log->setMethod($mode);
        $log->setMethod('purge');
        $log->setIndexName($replicaName);
        $this->logs[] = $log;

        return $this;
    }

    public function startTime(float $time): void
    {
        if (!$this->isEnabled()) {
            return;
        }
        /** @var Log $last */
        $last = $this->logs[\count($this->logs) - 1];
        $last->setExecutionTime(microtime(true) - $time);
    }

    public function logs(): array
    {
        return $this->logs;
    }
}

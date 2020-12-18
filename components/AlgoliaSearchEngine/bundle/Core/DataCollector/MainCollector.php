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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class MainCollector extends DataCollector
{
    /**
     * @var Logger
     */
    private $collector;

    public function __construct(Logger $collector)
    {
        $this->collector = $collector;
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        $logs = $this->collector->logs();
        $this->data = [
            'queryCount' => \count($logs),
            'queries' => $this->collector->logs(),
        ];
    }

    public function getQueries(): array
    {
        return [
            'count' => $this->data['queryCount'],
            'list' => $this->data['queries'],
        ];
    }

    public function reset(): void
    {
        $this->data = [];
    }

    public function getName(): string
    {
        return 'nova.ez.algolia.collector';
    }
}

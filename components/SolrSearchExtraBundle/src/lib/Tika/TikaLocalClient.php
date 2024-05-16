<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Tika;

use RuntimeException;
use SplFileInfo;
use Symfony\Component\Process\Process;

/**
 * Class TikaLocalClient.
 *
 * @package Novactive\EzSolrSearchExtra\Tika
 */
class TikaLocalClient implements TikaClientInterface
{
    /**
     * Client constructor.
     */
    public function __construct(protected string $jar)
    {
    }

    /**
     * @param $command
     */
    protected function run($command): string
    {
        $shellCommand = [
            'java',
            '-Dpdfbox.fontcache=/tmp',
            '-jar',
            $this->jar,
        ];
        $shellCommand = array_merge($shellCommand, explode(" ", $command));

        $process = new Process($shellCommand);
        $process->setWorkingDirectory(__DIR__.'/../../../');
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException($process->getErrorOutput());
        }

        return $process->getOutput();
    }

    /**
     * {@inheritdoc}
     */
    public function getText($fileName): ?string
    {
        $file = new SplFileInfo($fileName);
        $command = sprintf('--text %s', $file->getRealPath());

        return $this->run($command);
    }
}

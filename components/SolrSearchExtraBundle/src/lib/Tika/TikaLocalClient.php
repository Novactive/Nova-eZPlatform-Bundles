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
    /** @var string */
    protected $jar;

    /**
     * Client constructor.
     */
    public function __construct(string $jar)
    {
        $this->jar = $jar;
    }

    /**
     * @param $command
     */
    protected function run($command): string
    {
        $shellCommand = sprintf('java -Dpdfbox.fontcache=/tmp -jar %s %s', $this->jar, $command);

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

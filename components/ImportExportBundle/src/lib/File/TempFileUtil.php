<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\File;

use RuntimeException;
use Symfony\Component\Uid\Uuid;

abstract class TempFileUtil
{
    /**
     * @var string[]
     */
    private static array $tempFiles = [];

    private function __construct()
    {
    }

    /**
     * @param string $tempFile path to a file
     */
    public static function addTempFile(string $tempFile): void
    {
        self::$tempFiles[] = $tempFile;
    }

    /**
     * @throw RuntimeException
     */
    public static function download(string $sourceUrl): string
    {
        $tmpFilePath = DIRECTORY_SEPARATOR.
                       trim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).
                       DIRECTORY_SEPARATOR.
                       ltrim((string) Uuid::v4(), DIRECTORY_SEPARATOR);

        $originalPathInfos = pathinfo(strtok($sourceUrl, '?'));
        if (!empty($originalPathInfos['extension'])) {
            $tmpFilePath .= '.'.$originalPathInfos['extension'];
        }

        $context = stream_context_create(
            [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]
        );

        $source = fopen(
            str_replace(' ', '+', $sourceUrl),
            'rb',
            false,
            $context
        );

        $dest = fopen(
            $tmpFilePath,
            'wb'
        );

        if (!$source) {
            throw new RuntimeException(sprintf('Could not open source file : %s', $sourceUrl));
        }

        if (!$dest) {
            throw new RuntimeException(sprintf('Could not open destination file : %s', $tmpFilePath));
        }

        stream_copy_to_stream($source, $dest);
        fclose($source);
        fclose($dest);

        self::addTempFile($tmpFilePath);

        return $tmpFilePath;
    }

    /**
     * Removes all previously added files from disk.
     */
    public static function removeTempFiles(): void
    {
        foreach (self::$tempFiles as $tempFile) {
            if (is_file($tempFile)) {
                unlink($tempFile);
            }
        }

        self::$tempFiles = [];
    }
}

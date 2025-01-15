<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use Symfony\Component\Uid\Uuid;

/**
 * Downloads a file from a given URL and saves it to a temporary location.
 * Return the path to the temporary file.
 *
 * @see https://www.php.net/manual/en/function.tempnam.php
 */
class DownloadToTmpTransformer extends AbstractItemValueTransformer
{
    protected function transform(mixed $value, array $options = []): ?string
    {
        if (empty($value)) {
            return null;
        }

        $tmpFilePath = DIRECTORY_SEPARATOR.
                trim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).
                DIRECTORY_SEPARATOR.
                ltrim((string) Uuid::v4(), DIRECTORY_SEPARATOR);

        $originalPathInfos = pathinfo($value);
        if (!empty($originalPathInfos['extension'])) {
            $tmpFilePath .= '.'.$originalPathInfos['extension'];
        }

        register_shutdown_function(function () use ($tmpFilePath) {
            if (file_exists($tmpFilePath)) {
                unlink($tmpFilePath);
            }
        });
        file_put_contents(
            $tmpFilePath,
            file_get_contents(
                str_replace(' ', '+', $value),
                false,
                stream_context_create(
                    [
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                        ],
                    ]
                )
            )
        );

        return $tmpFilePath;
    }
}

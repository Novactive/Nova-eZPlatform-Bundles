<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use Symfony\Component\Uid\Uuid;

class DownloadToTmpTransformer extends AbstractItemValueTransformer
{
    public function transform($value, array $options = [])
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

        file_put_contents(
            $tmpFilePath,
            file_get_contents(
                $value,
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

        register_shutdown_function(function () use ($tmpFilePath) {
            unlink($tmpFilePath);
        });

        return $tmpFilePath;
    }
}

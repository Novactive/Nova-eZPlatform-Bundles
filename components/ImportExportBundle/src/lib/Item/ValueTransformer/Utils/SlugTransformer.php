<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use Ibexa\Core\Persistence\Legacy\Content\UrlAlias\SlugConverter;

/**
 * Transforms a string to its slug representation.
 */
class SlugTransformer extends AbstractItemValueTransformer
{
    public function __construct(
        protected SlugConverter $slugConverter
    ) {
    }

    /**
     * @param string|string[]|null $value
     *
     * @return string|string[]|null
     */
    protected function transform(mixed $value, array $options = []): mixed
    {
        if (null == $value) {
            return null;
        }

        if (is_array($value)) {
            $values = [];
            foreach ($value as $string) {
                $values[] = $this->slugConverter->convert($string);
            }

            return $values;
        }

        return $this->slugConverter->convert($value);
    }
}

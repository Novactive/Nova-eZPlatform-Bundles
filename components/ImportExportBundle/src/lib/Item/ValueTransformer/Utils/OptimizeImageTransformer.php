<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Liip\ImagineBundle\Model\Binary;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OptimizeImageTransformer extends AbstractItemValueTransformer
{
    public function __construct(
        protected FilterManager $filterManager,
    ) {
    }

    /**
     * @param mixed $value the file path to the image
     */
    protected function transform(mixed $value, array $options = []): ?string
    {
        if (empty($value)) {
            return null;
        }

        $pathInfos = pathinfo($value);
        $imageData = getimagesize($value);

        $binary = new Binary(
            file_get_contents($value),
            $imageData['mime'],
            $pathInfos['extension']
        );

        $binary = $this->filterManager->applyFilter($binary, $options['variation']);
        file_put_contents($value, $binary->getContent());

        return $value;
    }

    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
        parent::configureOptions($optionsResolver);
        $optionsResolver->define('variation')
                        ->required();
    }
}

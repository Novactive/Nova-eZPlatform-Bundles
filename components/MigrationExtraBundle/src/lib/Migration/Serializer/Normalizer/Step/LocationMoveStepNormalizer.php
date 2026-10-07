<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaMigrationExtra\Migration\Serializer\Normalizer\Step;

use AlmaviaCX\Bundle\IbexaMigrationExtra\Migration\ValueObject\Step\LocationMoveStep;
use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\ValueObject\Location\Matcher;
use Ibexa\Migration\ValueObject\Step\StepInterface;

class LocationMoveStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'location';
    }

    public function getMode(): string
    {
        return 'move';
    }

    public function getHandledClassType(): string
    {
        return LocationMoveStep::class;
    }

    /**
     * @param LocationMoveStep     $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     locationMatch: mixed,
     *     newParentLocationMatch: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $object, ?string $format = null, array $context = []): array
    {
        return [
            'locationMatch' => $this->normalizer->normalize($object->locationMatch, $format, $context),
            'newParentLocationMatch' => $this->normalizer->normalize($object->newParentLocationMatch, $format, $context)
        ];
    }

    /**
     * @param array{
     *     locationMatch: array<mixed>,
     *     newParentLocationMatch: array<mixed>,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return LocationMoveStep
     */
    protected function denormalizeStep($data, string $type, ?string $format = null, array $context = []): StepInterface
    {
        $locationMatch = $this->denormalizer->denormalize(
            $data['locationMatch'],
            Matcher::class,
            $format,
            $context
        );

        $newParentLocationMatch = $this->denormalizer->denormalize(
            $data['newParentLocationMatch'],
            Matcher::class,
            $format,
            $context
        );

        return new LocationMoveStep(
            $locationMatch,
            $newParentLocationMatch,
        );
    }
}

<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaMigrationExtra\Migration\StepExecutor\ReferenceDefinition\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\Content\ContentResolverInterface;
use Ibexa\Migration\ValueObject\Reference\Reference;
use Webmozart\Assert\Assert;

class TaxonomyIdResolverInterface implements ContentResolverInterface
{
    public function __construct(
        protected TaxonomyServiceInterface $taxonomyService
    ) {
    }

    public static function getHandledType(): string
    {
        return 'taxonomy_id';
    }

    public function resolve(ReferenceDefinition $referenceDefinition, Content $content): Reference
    {
        $taxonomy = $this->taxonomyService->loadEntryByContentId($content->id);
        Assert::notNull(
            $taxonomy,
            'Content object isn\'t a taxonomy entry.'
        );

        return Reference::create(
            $referenceDefinition->getName(),
            $taxonomy->getId(),
        );
    }
}

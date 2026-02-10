<?php

declare(strict_types=1);

use Ibexa\Contracts\Rector\Sets\IbexaSetList;
use Rector\Config\RectorConfig;
use Rector\Symfony\Set\SymfonySetList;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/bin',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/components/SEOBundle',
    ])
    // uncomment to reach your current PHP version
    ->withPhpSets()
    ->withTypeCoverageLevel(0)
    ->withDeadCodeLevel(10)
    ->withCodeQualityLevel(10)
    ->withAttributesSets()
    ->withComposerBased(
        twig: true,
        symfony: true,
        doctrine: true,
    )
    ->withSets(
        [
                   IbexaSetList::IBEXA_50->value,
               ]
    )
    ->withRules(
        [
            DeclareStrictTypesRector::class,
        ]
    );

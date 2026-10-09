<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaTranslationUiBundle\DependencyInjection\Security;

use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\YamlPolicyProvider;

final class TranslationPolicyProvider extends YamlPolicyProvider
{
    protected function getFiles(): array
    {
        return [
            __DIR__ . '/../../Resources/config/policies.yml',
        ];
    }
}

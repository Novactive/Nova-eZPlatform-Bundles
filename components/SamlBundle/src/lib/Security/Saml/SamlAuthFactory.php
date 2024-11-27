<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaSaml\Security\Saml;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Exception\ParameterNotFoundException;
use OneLogin\Saml2\Auth;

class SamlAuthFactory
{
    protected ConfigResolverInterface $configResolver;
    protected array $defaultSettings;

    public function __construct(
        ConfigResolverInterface $configResolver,
        array $defaultSettings
    ) {
        $this->defaultSettings = $defaultSettings;
        $this->configResolver = $configResolver;
    }

    public function __invoke(): Auth
    {
        $settings = $this->defaultSettings;
        try {
            $saSettings = $this->configResolver->getParameter('auth_settings', 'almaviacx.saml');
        } catch (ParameterNotFoundException $exception) {
            $saSettings = [];
        }

        return new Auth($this->mergeSettings($settings, $saSettings));
    }

    protected function mergeSettings(array $defaultSettings, array $settings): array
    {
        foreach ($defaultSettings as $key => $setting) {
            if (!isset($settings[$key])) {
                continue;
            }
            if (is_array($setting)) {
                $defaultSettings[$key] = $this->mergeSettings($defaultSettings[$key], $settings[$key]);
            } else {
                $defaultSettings[$key] = $settings[$key];
            }
        }

        return $defaultSettings;
    }
}

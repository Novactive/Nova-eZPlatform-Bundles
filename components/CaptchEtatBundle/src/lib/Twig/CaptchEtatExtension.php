<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\CaptchEtat\Twig;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Locale\LocaleConverterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CaptchEtatExtension extends AbstractExtension
{
    protected ConfigResolverInterface $configResolver;
    protected LocaleConverterInterface $localeConverter;

    public function __construct(
        ConfigResolverInterface $configResolver,
        LocaleConverterInterface $localeConverter,
    ) {
        $this->localeConverter = $localeConverter;
        $this->configResolver = $configResolver;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('captchetat_type', [$this, 'getCaptchaType']),
        ];
    }

    public function getCaptchaType(?string $languageCode = null): string
    {
        return $this->getType($languageCode ?? $this->getShortLanguage());
    }

    protected function getShortLanguage(): string
    {
        $languageCode = $this->configResolver->getParameter('languages')[0];
        $posixLocale = $this->localeConverter->convertToPOSIX($languageCode);

        return substr($posixLocale, 0, 2);
    }

    public function getType(string $lang): string
    {
        $type = 'numerique6_7CaptchaFR';
        if ('fr' !== $lang) {
            $type = 'numerique6_7CaptchaEN';
        }

        $type = empty($this->captchetatType) ? $type : $this->captchetatType;

        return $type;
    }
}

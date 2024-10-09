<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\CaptchEtat\Challenge;

use AlmaviaCX\Bundle\CaptchEtat\Api\Gateway;
use AlmaviaCX\Bundle\CaptchEtat\Logger\CaptchEtatLogger;
use AlmaviaCX\Bundle\CaptchEtat\Value\CaptchEtatChallenge;
use DOMDocument;
use Exception;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Locale\LocaleConverterInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChallengeGenerator
{
    protected ConfigResolverInterface $configResolver;
    protected LocaleConverterInterface $localeConverter;
    protected TranslatorInterface $translator;
    protected CaptchEtatLogger $logger;
    protected Gateway $gateway;

    public function __construct(
        LocaleConverterInterface $localeConverter,
        ConfigResolverInterface $configResolver,
        Gateway $gateway,
        TranslatorInterface $translator,
        CaptchEtatLogger $logger
    ) {
        $this->gateway = $gateway;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->localeConverter = $localeConverter;
        $this->configResolver = $configResolver;
    }

    public function __invoke(): CaptchEtatChallenge
    {
        $lang = $this->getShortLanguage();

        return CaptchEtatChallenge::createLazyGhost(function (CaptchEtatChallenge $instance) use ($lang) {
            try {
                $captchaHtml = $this->getCaptchaHtml($lang);
                $crawler = new Crawler($captchaHtml);
                $type = 'numerique6_7CaptchaFR';
                if ('fr' !== $lang) {
                    $type = 'numerique6_7CaptchaEN';
                }
                $captchaId = $crawler->filter('#BDC_VCID_'.$type)->attr('value');
                $instance->__construct($captchaHtml, $captchaId);
            } catch (Exception $exception) {
                $this->logger->logException($exception);
                $instance->__construct(null, null);
            }
        });
    }

    protected function getCaptchaHtml(string $lang): string
    {
        $type = 'numerique6_7CaptchaFR';
        if ('fr' !== $lang) {
            $type = 'numerique6_7CaptchaEN';
        }

        $html = $this->gateway->getSimpleCaptchaEndpoint(
            'html',
            'frontal',
            null,
            $type
        );
        $hidden = 'style="visibility: hidden !important"';
        $html = str_replace($hidden, '', $html);
        // Change the alt of image
        return $this->changeImageTitle($html);
    }

    protected function changeImageTitle(string $html): string
    {
        try {
            $doc = new DOMDocument();
            $doc->loadHTML('<?xml encoding="UTF-8">'.$html);
            $elements = $doc->getElementsByTagName('img');
            foreach ($elements as $item) {
                if ('BDC_CaptchaImage' === $item->getAttribute('class')) {
                    $item->setAttribute('alt', $this->translator->trans('image_title', [], 'captchetat'));
                    break;
                }
            }

            return $doc->saveHTML();
        } catch (\Throwable $e) {
            return $html;
        }
    }

    protected function getShortLanguage(): string
    {
        $languageCode = $this->configResolver->getParameter('languages')[0];
        $posixLocale = $this->localeConverter->convertToPOSIX($languageCode);

        return substr($posixLocale, 0, 2);
    }
}

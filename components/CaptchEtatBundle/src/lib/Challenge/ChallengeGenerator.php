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
    protected string $captchetatType;

    public function __construct(
        LocaleConverterInterface $localeConverter,
        ConfigResolverInterface $configResolver,
        Gateway $gateway,
        TranslatorInterface $translator,
        CaptchEtatLogger $logger,
        string $captchetatType
    ) {
        $this->gateway = $gateway;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->localeConverter = $localeConverter;
        $this->configResolver = $configResolver;
        $this->captchetatType = $captchetatType;
    }

    public function __invoke()
    {
        $lang = $this->getShortLanguage();
        $type = $this->getType($lang);
        $image = $this->gateway->getSimpleCaptchaEndpoint(
            'image',
            'frontal',
            null,
            $type
        );

        return $image;

        return CaptchEtatChallenge::createLazyGhost(function (CaptchEtatChallenge $instance) use ($lang) {
            try {
                $captchaHtml = $this->getCaptchaHtml($lang);
                $crawler = new Crawler($captchaHtml);

                $type = $this->getType($lang);
                $captchaId = $crawler->filter('#BDC_VCID_'.$type)->attr('value');
                $instance->__construct($crawler->filter('#frontal')->outerHtml(), $captchaId);
            } catch (Exception $exception) {
                $this->logger->logException($exception);
                $instance->__construct(null, null);
            }
        });
    }

    protected function getCaptchaHtml(string $lang): string
    {
        $type = $this->getType($lang);
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

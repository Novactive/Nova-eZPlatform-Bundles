<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaTranslationUi\Translation;

use Lexik\Bundle\TranslationBundle\Translation\Translator as LexikTranslator;
use Psr\Container\ContainerInterface;
use Symfony\Component\Translation\Formatter\MessageFormatterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Translator extends LexikTranslator
{
    protected TranslatorInterface $defaultTranslator;

    public function __construct(
        ContainerInterface $container,
        MessageFormatterInterface $formatter,
        string $defaultLocale,
        array $loaderIds = [],
        array $options = [],
        array $enabledLocales = []
    ) {
        parent::__construct($container, $formatter, $defaultLocale, $loaderIds, $options, $enabledLocales);
    }

    public function setDefaultTranslator(TranslatorInterface $defaultTranslator): void
    {
        $this->defaultTranslator = $defaultTranslator;
    }

    public function trans(?string $id, array $parameters = [], string $domain = null, string $locale = null)
    {
        if (null === $id || '' === $id) {
            return '';
        }

        if (null === $domain) {
            $domain = 'messages';
        }

        $catalogue = $this->getCatalogue($locale);
        if (!$catalogue->defines($id, $domain)) {
            return $this->defaultTranslator->trans($id, $parameters, $domain, $locale);
        }

        return parent::trans($id, $parameters, $domain, $locale);
    }
}

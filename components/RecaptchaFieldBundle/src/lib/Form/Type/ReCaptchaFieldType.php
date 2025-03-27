<?php

declare(strict_types=1);

namespace Novactive\Bundle\AlmaviaCXIbexaRecaptchaFieldBundle\Form\Type;

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaV3Type;
use Ibexa\FormBuilder\Form\Type\Field\AbstractFieldType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class ReCaptchaFieldType extends AbstractFieldType
{
    private const GOOGLE_RECAPTCHA_VERSION = 3;
    private ParameterBagInterface $params;
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function getParent(): string
    {
        return $this->params->get('ewz_recaptcha.version') === self::GOOGLE_RECAPTCHA_VERSION ?
            EWZRecaptchaV3Type::class :
            EWZRecaptchaType::class;
    }
}

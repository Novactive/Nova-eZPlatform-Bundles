<?php

declare(strict_types=1);

namespace Novactive\Bundle\IbexaRecaptchaField\FormBuilder\Field\Mapper;

use Ibexa\FormBuilder\FieldType\Field\Mapper\GenericFieldMapper;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use EzSystems\EzPlatformFormBuilder\FieldType\Model\Field;

final class ReCaptchaMapper extends GenericFieldMapper
{
    private const GOOGLE_RECAPTCHA_VERSION = 3;
    private ParameterBagInterface $params;

    public function __construct(string $fieldIdentifier, string $formType, ParameterBagInterface $params)
    {
        parent::__construct($fieldIdentifier, $formType);
        $this->params = $params;
    }

    protected function mapFormOptions(Field $field, array $constraints): array
    {
        $options = parent::mapFormOptions($field, $constraints);
        $options['field'] = $field;
        $options['label'] = $field->getName();
        $options['attr'] = [
            'theme' => $field->getAttributeValue('theme'),
            'type' => 'image',
            'size' => $field->getAttributeValue('size'),
            'defer' => true,
            'async' => true
        ];

        if ($this->params->get('ewz_recaptcha.version') !== self::GOOGLE_RECAPTCHA_VERSION) {
            $options['attr'] = ['options' => $options['attr']];
        }

        $options['constraints'] = [
            $this->params->get('ewz_recaptcha.version') === self::GOOGLE_RECAPTCHA_VERSION ?
                new Recaptcha\IsTrueV3() :
                new Recaptcha\IsTrue()
        ];

        return $options;
    }
}

<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\CaptchEtat\FormBuilder\FieldType\Field\Mapper;

use AlmaviaCX\Bundle\CaptchEtat\Challenge\ChallengeGenerator;
use AlmaviaCX\Bundle\CaptchEtat\Form\Type\CaptchEtatType;
use Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;
use Ibexa\FormBuilder\FieldType\Field\Mapper\ButtonFieldMapper;
use Symfony\Component\Form\FormBuilderInterface;

class ButtonFieldMapperDecorator implements FieldMapperInterface
{
    protected ButtonFieldMapper $buttonFieldMapper;
    protected ChallengeGenerator $challengeGenerator;

    public function __construct(
        ButtonFieldMapper $buttonFieldMapper,
        ChallengeGenerator $challengeGenerator
    ) {
        $this->buttonFieldMapper = $buttonFieldMapper;
        $this->challengeGenerator = $challengeGenerator;
    }

    public function mapField(FormBuilderInterface $builder, Field $field, array $constraints = []): void
    {
        if (!$builder->has('captcha')) {
            $builder->add('captcha', CaptchEtatType::class, [
                'label' => 'customform.show.captcha',
            ]);
        }
        $this->buttonFieldMapper->mapField($builder, $field, $constraints);
    }

    public function getSupportedField(): string
    {
        return $this->buttonFieldMapper->getSupportedField();
    }
}

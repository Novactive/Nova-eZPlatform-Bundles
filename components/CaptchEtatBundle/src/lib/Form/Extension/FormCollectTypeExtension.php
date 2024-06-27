<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\CaptchEtat\Form\Extension;

use AlmaviaCX\Bundle\CaptchEtat\Event\Subscriber\AddCaptchaSubscriber;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class FormCollectTypeExtension extends AbstractTypeExtension
{
    public function getExtendedType(): string
    {
        return FormCollectTypeExtension::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventSubscriber(new AddCaptchaSubscriber());
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormCollectTypeExtension::class];
    }
}

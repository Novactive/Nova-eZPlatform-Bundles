<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComponentOptionsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $showInitialized = $options['show_initialized'];
        /** @var ComponentOptions|null $defaultConfiguration */
        $defaultConfiguration = $options['default_configuration'];

        if (false === $showInitialized && $defaultConfiguration) {
            $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) use ($defaultConfiguration) {
                    $form = $event->getForm();

                    $initializedOptions = $defaultConfiguration->getInitializedOptions();
                    foreach ($initializedOptions as $initializedOption) {
                        if ($form->has($initializedOption)) {
                            $form->remove($initializedOption);
                        }
                    }
                }
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->define('default_configuration')->required()->allowedTypes(AbstractComponent::getOptionsType());
        $resolver->setDefaults([
            'default_configuration' => null,
            'show_initialized' => false,
            'data_class' => AbstractComponent::getOptionsType(),
            'translation_domain' => 'forms',
        ]);
    }
}

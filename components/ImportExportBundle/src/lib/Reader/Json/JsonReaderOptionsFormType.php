<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Json;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderOptionsFormType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JsonReaderOptionsFormType extends ReaderOptionsFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('json', TextareaType::class, [
                'label' => /* @Desc("JSON") */ 'text_reader.form.options.json.label',
                'required' => false,
            ]);

        $modelTransformer = new CallbackTransformer(
            function (?JsonReaderOptions $data) {
                return [
                    'json' => $data ? json_encode($data->json) : null,
                ];
            },
            function ($data) {
                return new JsonReaderOptions([
                    'json' => !empty($data['json']) ? json_decode($data['json'], true) : null,
                ]);
            }
        );
        $builder->addModelTransformer($modelTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => null,
            'translation_domain' => 'forms',
        ]);
    }
}

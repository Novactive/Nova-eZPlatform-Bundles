<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Ibexa\ContentList;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderOptionsFormType;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IbexaContentListReaderOptionsFormType extends ReaderOptionsFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder->add('parentLocationId', IntegerType::class, [
            'label' => /* @Desc("Parent location id") */ 'reader.ibexa.content_list.options.parentLocationId.label',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => IbexaContentListReader::getOptionsType(),
            'translation_domain' => 'forms',
                               ]);
    }
}

<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Stream;

use AlmaviaCX\Bundle\IbexaImportExport\Resolver\FilepathResolver;
use AlmaviaCX\Bundle\IbexaImportExport\Writer\WriterOptionsFormType;
use JMS\TranslationBundle\Annotation\Desc;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

class StreamWriterOptionsFormType extends WriterOptionsFormType implements TranslationContainerInterface
{
    protected FilepathResolver $filepathResolver;

    public function __construct(FilepathResolver $filepathResolver)
    {
        $this->filepathResolver = $filepathResolver;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $tokens = implode(
            ' / ',
            array_keys($this->filepathResolver->buildTokens())
        );
        $builder->add('filepath', TextType::class, [
            'label' => /* @Desc("File path") */ 'writer.stream.options.filepath.label',
            'help' => new TranslatableMessage('writer.stream.options.filepath.tokens', ['%tokens%' => $tokens]),
        ]);
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('writer.stream.options.filepath.tokens', 'forms'))
                ->setDesc('Tokens: %tokens%'),
        ];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => AbstractStreamWriter::getOptionsType(),
            'translation_domain' => 'forms',
        ]);
    }
}

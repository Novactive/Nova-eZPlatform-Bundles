<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\CaptchEtat\Form\Type;

use AlmaviaCX\Bundle\CaptchEtat\Validator\Constraint\CaptchEtatValidChallenge;
use AlmaviaCX\Bundle\CaptchEtat\Value\CaptchEtatChallenge;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CaptchEtatType extends AbstractType implements TranslationContainerInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $challenge = $options['challenge'] ?? null;
        if ($challenge instanceof CaptchEtatChallenge) {
            $builder->add(
                'answer',
                TextType::class,
                [
                    'label' => 'form.captcha.input_answer',
                    'help' => 'form.captcha.help',
                    'required' => true,
                    'attr' => ['value' => ''],
                ]
            );
            $builder->add(
                'captcha_id',
                HiddenType::class,
                [
                    'attr' => ['value' => $challenge->getCaptchaId()],
                ]
            );
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $challenge = $options['challenge'] ?? null;
        if ($challenge instanceof CaptchEtatChallenge) {
            $view->vars['display_captcha'] = true;
            $view->vars['captcha_html'] = $challenge->getCaptchaHtml();
        } else {
            $view->vars['display_captcha'] = false;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('challenge')
                 ->default(null)
                 ->allowedTypes(CaptchEtatChallenge::class, 'null');

        $resolver->setDefaults([
                                    'constraints' => [
                                        new CaptchEtatValidChallenge(),
                                    ],
                                    'mapped' => false,
                                    'compound' => true,
                                    'attr' => [
                                        'class' => 'captcha-widget-container',
                                    ],
                                ]);
    }

    public function getBlockPrefix()
    {
        return 'captchetat';
    }

    public function getName(): string
    {
        return 'captchetat';
    }

    public static function getTranslationMessages()
    {
        return [
            ( new Message('form.captcha.input_answer', 'messages') )->setDesc('Captcha answer'),
            ( new Message('form.captcha.help', 'messages') )
                ->setDesc('To view a new code or listen to the code, use the buttons next to the image.'),
        ];
    }
}

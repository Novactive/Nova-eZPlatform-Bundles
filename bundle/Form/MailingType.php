<?php
/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Form;

use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MailingType.
 */
class MailingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $fromArray  = function ($array) {
            return implode(',', $array);
        };
        $fromString = function ($string) {
            return array_unique(null !== $string ? explode(',', $string) : []);
        };

        $builder
            ->add(
                'names',
                CollectionType::class,
                [
                    'label'        => false,
                    'allow_add'    => false,
                    'allow_delete' => false,
                    'entry_type'   => TextType::class,
                    'required'     => true,
                ]
            )
            ->add('recurring', CheckboxType::class, ['label' => 'Is it a reccuring Mailing?'])
            ->add('locationId', HiddenType::class)
            ->add(
                'hoursOfDay',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add('daysOfWeek', TextType::class)
            ->add('daysOfMonth', TextType::class)
            ->add('daysOfYear', TextType::class)
            ->add('weeksOfMonth', TextType::class)
            ->add('monthsOfYear', TextType::class)
            ->add('weeksOfYear', TextType::class);

        $transformationFields = [
            'hoursOfDay',
            'daysOfWeek',
            'daysOfMonth',
            'daysOfYear',
            'weeksOfMonth',
            'monthsOfYear',
            'weeksOfYear',
        ];

        foreach ($transformationFields as $field) {
            $builder->get($field)->addModelTransformer(new CallbackTransformer($fromArray, $fromString));
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Mailing::class,
            ]
        );
    }
}

<?php

/**
 * NovaeZMaintenanceBundle.
 *
 * @package   Novactive\NovaeZMaintenanceBundle
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZMaintenanceBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\NovaeZMaintenanceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $classes = 'flatpickr flatpickr-input ez-data-source__input form-control';
        $builder
            ->add(
                'min_created',
                TextType::class,
                [
                    'label' => 'Starts',
                    'required' => true,
                    'attr' => [
                        'class' => $classes.' date-start',
                    ],
                ]
            )
            ->add(
                'max_created',
                TextType::class,
                [
                    'label' => 'Ends',
                    'required' => true,
                    'attr' => [
                        'class' => $classes.' date-end',
                    ],
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Enable',
                    'attr' => [
                        'class' => 'btn btn-primary',
                    ],
                ]
            );
    }
}

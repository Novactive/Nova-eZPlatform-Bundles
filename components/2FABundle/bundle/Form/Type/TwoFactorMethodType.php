<?php

/**
 * NovaeZ2FABundle.
 *
 * @package   NovaeZ2FABundle
 *
 * @author    Maxim Strukov <maxim.strukov@almaviacx.com>
 * @copyright 2021 AlmaviaCX
 * @license   https://github.com/Novactive/NovaeZ2FA/blob/main/LICENSE
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZ2FABundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

final class TwoFactorMethodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'method',
                ChoiceType::class,
                [
                    'label' => 'Select Method',
                    'expanded' => true,
                    'multiple' => false,
                    'choices' => [
                        'setup_form.method.mobile' => 'app',
                        'setup_form.method.email' => 'email',
                    ],
                    'data' => 'app',
                ]
            )
            ->add('submit', SubmitType::class);
    }
}

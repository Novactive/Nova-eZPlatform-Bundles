<?php

/*
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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;

final class TwoFactorAuthType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'sixdigitCode',
                TextType::class,
                [
                    'required' => true,
                    'label' => '6-digit code',
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => '6-digit code is required to complete the setup',
                            ]
                        )
                    ]
                ]
            )
            ->add('secretKey', HiddenType::class, ['required' => true])
            ->add('submit', SubmitType::class);
    }
}
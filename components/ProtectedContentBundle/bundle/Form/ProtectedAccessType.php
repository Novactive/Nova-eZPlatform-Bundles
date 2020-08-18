<?php

/**
 * NovaeZProtectedContentBundle.
 *
 * @package   Novactive\Bundle\eZProtectedContentBundle
 *
 * @author    Novactive
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/eZProtectedContentBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZProtectedContentBundle\Form;

use Novactive\Bundle\eZProtectedContentBundle\Entity\ProtectedAccess;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProtectedAccessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('contentId', HiddenType::class, ['required' => true])
                ->add(
                    'protectChildren',
                    CheckboxType::class,
                    ['label' => 'tab.table.th.children_protection', 'required' => false]
                )
                ->add('enabled', CheckboxType::class, ['label' => 'tab.table.th.enabled', 'required' => false])
                ->add('password', TextType::class, ['required' => true, 'label' => 'tab.table.th.password']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => ProtectedAccess::class,
                'translation_domain' => 'ezprotectedcontent',
            ]
        );
    }
}

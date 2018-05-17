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

use EzSystems\EzPlatformAdminUi\Siteaccess\SiteaccessResolver;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MailingType.
 */
class MailingType extends AbstractType
{
    /**
     * @var SiteaccessResolver
     */
    private $siteAccessResolver;

    /**
     * CampaignType constructor.
     *
     * @param SiteaccessResolver $siteAccessResolver
     */
    public function __construct(SiteaccessResolver $siteAccessResolver)
    {
        $this->siteAccessResolver = $siteAccessResolver;
    }

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

        $siteaccesses = array_combine(
            array_values($this->siteAccessResolver->getSiteaccesses()),
            array_values($this->siteAccessResolver->getSiteaccesses())
        );

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
            ->add('subject', TextType::class, ['required' => false])
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

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($siteaccesses) {
                $form            = $event->getForm();
                $mailing         = $event->getData();
                $siteaccessLimit = $mailing->getCampaign()->getSiteaccessLimit() ?? [];
                $siteaccessLimit = array_combine(
                    array_values($siteaccessLimit),
                    array_values($siteaccessLimit)
                );
                /* @var Mailing $mailing */
                $form->add(
                    'siteAccess',
                    ChoiceType::class,
                    [
                        'label'    => 'On which siteaccess?',
                        'choices'  => count($siteaccessLimit) > 0 ? $siteaccessLimit : $siteaccesses,
                        'expanded' => true,
                        'multiple' => false,
                        'required' => true,
                    ]
                );
            }
        );
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

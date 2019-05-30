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
use Novactive\Bundle\eZMailingBundle\Entity\MailingList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MailingListType.
 */
class MailingListType extends AbstractType
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
        $siteaccess = array_combine(
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
            ->add('withApproval', CheckboxType::class, [
                'required' => false,
                'label'    => 'mailinglisttype.buildform.withapproval',
                ])
            ->add(
                'siteaccessLimit',
                ChoiceType::class,
                [
                    'expanded' => true,
                    'multiple' => true,
                    'choices'  => $siteaccess,
                    'label'    => 'mailinglisttype.buildform.siteaccess_limit',
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class'         => MailingList::class,
                'translation_domain' => 'ezmailing',
            ]
        );
    }
}

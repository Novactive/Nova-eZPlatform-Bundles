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
use Novactive\Bundle\eZMailingBundle\Entity\Campaign;
use Novactive\Bundle\eZMailingBundle\Entity\MailingList;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CampaignType.
 */
class CampaignType extends AbstractType
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
            ->add('senderName', TextType::class, ['required' => true])
            ->add('senderEmail', EmailType::class, ['required' => true])
            ->add('reportEmail', EmailType::class, ['required' => true])
            ->add('returnPathEmail', EmailType::class, ['required' => true])
            ->add('locationId', HiddenType::class)
            ->add(
                'siteaccessLimit',
                ChoiceType::class,
                [
                    'expanded' => true,
                    'multiple' => true,
                    'choices'  => $siteaccesses,
                ]
            )
            ->add(
                'mailingLists',
                EntityType::class,
                [
                    'class'    => MailingList::class,
                    'expanded' => true,
                    'multiple' => true,
                    'required' => true,
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
                'data_class' => Campaign::class,
            ]
        );
    }
}

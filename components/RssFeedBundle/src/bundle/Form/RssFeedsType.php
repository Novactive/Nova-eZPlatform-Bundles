<?php

/**
 * NovaeZRssFeedBundle.
 *
 * @package   NovaeZRssFeedBundle
 *
 * @author    Novactive
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZRssFeedBundle/blob/master/LICENSE
 */

namespace Novactive\EzRssFeedBundle\Form;

use Novactive\EzRssFeedBundle\Entity\RssFeeds;
use Novactive\EzRssFeedBundle\Form\Transformer\MultipleChoicesTransformer;
use Novactive\EzRssFeedBundle\Services\SiteListServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RssFeedsType extends AbstractType
{
    protected SiteListServiceInterface $siteListService;
    protected MultipleChoicesTransformer $choicesTransformer;

    public function __construct(
        SiteListServiceInterface $siteListService,
        MultipleChoicesTransformer $choicesTransformer
    ) {
        $this->choicesTransformer = $choicesTransformer;
        $this->siteListService = $siteListService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'title',
            TextType::class,
            [
                'label_format' => 'ez_rss_feed.form.title',
            ]
        )
        ->add(
            'description',
            TextType::class,
            [
                'label' => 'ez_rss_feed.form.description',
            ]
        )
        ->add(
            'url_slug',
            TextType::class,
            [
                'label' => 'ez_rss_feed.form.url_slug',
            ]
        )
        ->add(
            'number_of_object',
            ChoiceType::class,
            [
                'label' => 'ez_rss_feed.form.number_of_object',
                'choices' => [
                    '5' => 5,
                    '10' => 10,
                    '20' => 20,
                    '30' => 30,
                    '40' => 40,
                    '50' => 50,
                ],
            ]
        )
        ->add(
            'sort_type',
            ChoiceType::class,
            [
                'label' => 'ez_rss_feed.form.sort_type',
                'choices' => [
                    'Date de publication' => RssFeeds::SORT_TYPE_PUBLICATION,
                    'Date de modification' => RssFeeds::SORT_TYPE_MODIFICATION,
                    'Par nom' => RssFeeds::SORT_TYPE_NAME,
                ],
            ]
        )
        ->add(
            'sort_direction',
            ChoiceType::class,
            [
                'label' => 'ez_rss_feed.form.sort_direction',
                'choices' => [
                    'Desc' => RssFeeds::SORT_DIRECTION_DESC,
                    'Asc' => RssFeeds::SORT_DIRECTION_ASC,
                ],
            ]
        )
        ->add(
            'feed_items',
            CollectionType::class,
            [
                'entry_type' => RssFeedItemsType::class,
                'entry_options' => [
                    'attr' => ['class' => 'rss_feeds_feed_item row'],
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'by_reference' => false,
            ]
        )->add(
            'feed_sites',
            ChoiceType::class,
            [
                'label' => 'ez_rss_feed.form.site_access',
                'choices' => $this->siteListService->addSiteAccessList(),
                'multiple' => true,
                'required' => false,

            ]
        );

        $builder->get('feed_sites')->addModelTransformer($this->choicesTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => RssFeeds::class,
            ]
        );
    }
}

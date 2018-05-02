<?php

namespace MC\Bundle\PrivateContentAccessBundle\Custom;

use eZ\Publish\API\Container;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\Core\Pagination\Pagerfanta\ContentSearchAdapter;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\Tab\Dashboard\PagerContentToDataMapper;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use MC\Bundle\PrivateContentAccessBundle\Entity\PrivateAccess;
use MC\Bundle\PrivateContentAccessBundle\Form\PrivateAccessForm;
use MC\Bundle\PrivateContentAccessBundle\MCPrivateContentAccessBundle;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class PrivateContentTab extends AbstractTab implements OrderedTabInterface
{
    /** @var PagerContentToDataMapper */
    protected $pagerContentToDataMapper;

    /** @var SearchService */
    protected $searchService;

    /** @var FormFactory */
    protected $formFactory;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        PagerContentToDataMapper $pagerContentToDataMapper,
        SearchService $searchService,
        FormFactory $formFactory

    ) {
        parent::__construct($twig, $translator);

        $this->pagerContentToDataMapper = $pagerContentToDataMapper;
        $this->searchService = $searchService;
        $this->formFactory = $formFactory;

    }

    public function getIdentifier(): string
    {
        return 'private-access';
    }

    public function getName(): string
    {
        return 'Private';
    }

    public function getOrder(): int
    {
        return 900;
    }

    public function renderView(array $parameters): string
    {
        $privateAccess = new PrivateAccess();

        return $this->twig->render('@MCPrivateContentAccess/tabs/private_content_tab.html.twig', [
            'name' => $this->getName()
        ]);
    }
}

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

    /** @var Request */
    //protected $request;

    /** @var FormFactory */
    protected $formFactory;

    /** @var service_container */
    //protected $container;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        PagerContentToDataMapper $pagerContentToDataMapper,
        SearchService $searchService,
        FormFactory $formFactory
        //Container $container

    ) {
        parent::__construct($twig, $translator);

        $this->pagerContentToDataMapper = $pagerContentToDataMapper;
        $this->searchService = $searchService;
        //$this->request = $request;
        $this->formFactory = $formFactory;

    }

    public function getIdentifier(): string
    {
        return 'make-private';
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
        //var_dump($parameters['content']);die();

        //$page = 1;
        //$limit = 10;

        //$query = new LocationQuery();

        //$query->sortClauses = [new SortClause\DateModified(LocationQuery::SORT_DESC)];
        //$query->query = new Criterion\LogicalAnd([
        //    new Criterion\ContentTypeIdentifier('article'),
        //]);

        /*$pager = new Pagerfanta(
            new ContentSearchAdapter($query,
                $this->searchService
            )
        );*/
        /*$pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $this->twig->render('PrivateContentBundle:tabs:private_content_tab.html.twig', [
            'data' => $this->pagerContentToDataMapper->map($pager),
        ]);*/



        // 1) build the form
        $privateAccess = new PrivateAccess();


        // $form = $this->container()->get("form.factory");
//        $form = $this->createForm(PrivateAccessForm::class, $privateAccess);

        // 2) handle the submit (will only happen on POST)
        /*$form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($makePrivate, $makePrivate->getPlainPassword());
            $makePrivate->setPassword($password);

            // 4) save the User!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($makePrivate);
            $entityManager->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('replace_with_some_route');
        }*/
/*
        return $this->render(
            'registration/register.html.twig',
            array('form' => $form->createView())
        );*/





        return $this->twig->render('@MCPrivateContentAccess/tabs/private_content_tab.html.twig', [
            'name' => $this->getName()
        ]);
    }
}

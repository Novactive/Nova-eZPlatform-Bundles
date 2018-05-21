<?php
namespace MC\Bundle\PrivateContentAccessBundle\Listener;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use Symfony\Component\Routing\Router;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PreContentViewListener
{
    private $permissionResolver;
    private $repository;
    private $contentService;
    private $em;
    private $router;
    private $session;

    public function __construct( PermissionResolver $permissionResolver, Repository $repository, ContentService $contentService, EntityManager $manager, $router, $session)
    {
        $this->permissionResolver = $permissionResolver;
        $this->repository = $repository;
        $this->contentService = $contentService;
        $this->em = $manager;
        $this->router = $router;
        $this->session = $session;
    }

    public function onPreContentView( PreContentViewEvent $event )
    {
        /**
         * @var $contentView ContentView
         */
        $contentView = $event->getContentView();
        $parameters = $contentView->getLocation();
        $location = $parameters['location']->getInnerLocation();
        $node_path = $location->pathString;

        $content = $this->contentService->loadContent($location->contentInfo->id);

        $current_user = $this->permissionResolver->getCurrentUserReference();

        $result = $this->em->getRepository('MCPrivateContentAccessBundle:PrivateAccess')->findOneBy(['location_path' => $node_path, 'activate' => 1]);

        $eZUser = $this->repository->getCurrentUser();

        $canRead = $this->permissionResolver->canUser('content','read',$content);

        if($result != NULL && $canRead){
            return RedirectResponse::create($this->router->generate('form_private_access'),301);
        }
    }
}
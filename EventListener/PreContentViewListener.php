<?php
/**
 * NovaeZExtraBundle PreContentViewListener
 *
 * @package   Novactive\Bundle\eZExtraBundle
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */
namespace Novactive\Bundle\eZExtraBundle\EventListener;

use eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent;
use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\MVC\Symfony\Templating\GlobalHelper;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;

/**
 * Class PreContentViewListener
 */
class PreContentViewListener
{
    /**
     * Repository eZ
     *
     * @var Repository
     */
    protected $repository;

    /**
     * Template Helper eZ
     *
     * @var GlobalHelper
     */
    protected $templateGlobalHelper;

    /**
     * Managed Types
     *
     * @var array
     */
    protected $types;

    /**
     * Constructor
     *
     * @param Repository   $repository
     * @param GlobalHelper $gHelper
     */
    public function __construct(Repository $repository, GlobalHelper $gHelper)
    {
        $this->repository           = $repository;
        $this->templateGlobalHelper = $gHelper;
    }

    /**
     * Add managed Type
     *
     * @param Type   $type
     * @param string $contentTypeIdentifier
     */
    public function addManagedType(Type $type, $contentTypeIdentifier)
    {
        $this->types[$contentTypeIdentifier] = $type;
    }

    /**
     * Get Type by Alias
     *
     * @param string $contentTypeIdentifier
     *
     * @return Type|false
     */
    public function getType($contentTypeIdentifier)
    {
        if (is_array($this->types) && array_key_exists($contentTypeIdentifier, $this->types)) {
            return $this->types[$contentTypeIdentifier];
        }

        return false;
    }

    /**
     * Inject variables in the view
     *
     * @param PreContentViewEvent $event
     */
    public function onPreContentView(PreContentViewEvent $event)
    {
        $contentView = $event->getContentView();

        $location = $content = null;
        /**
         * @var ContentView $contentView
         */
        $viewType = $contentView->getViewType();
        if ($contentView instanceof ContentView) {
            $location = $contentView->getLocation();
            $content  = $contentView->getContent();
        }

        if (is_string($viewType) && $location instanceof Location) {
            if ($location->invisible == 1 or $location->hidden == 1) {
                throw new NotFoundHttpException("Page not found");
            }

            $identifier = $this->repository->getContentTypeService()->loadContentType(
                $location->contentInfo->contentTypeId
            )->identifier;

            if ($type = $this->getType($identifier)) {
                $type->setContentView($contentView);
                $type->setLocation($location);
                $type->setContent($content);

                $children = [];

                $method = "get".preg_replace_callback(
                        '/(?:^|_)(.?)/',
                        create_function('$matches', 'return strtoupper($matches[1]);'),
                        $viewType
                    )."Children";

                if (method_exists($type, $method)) {
                    $children = $type->$method(
                        $this->templateGlobalHelper->getViewParameters(),
                        $this->templateGlobalHelper->getSiteaccess()
                    );
                } elseif ($viewType == 'full' && method_exists($type, 'getChildren')) {
                    $children = $type->getChildren(
                        $this->templateGlobalHelper->getViewParameters(),
                        $this->templateGlobalHelper->getSiteaccess()
                    );
                }

                $contentView->addParameters(['children' => $children]);
            }
        }
    }
}

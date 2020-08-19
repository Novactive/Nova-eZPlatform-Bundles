<?php

/**
 * NovaeZExtraBundle PreContentViewListener.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZExtraBundle\EventListener;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent;
use eZ\Publish\Core\MVC\Symfony\Templating\GlobalHelper;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PreContentViewListener
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var GlobalHelper
     */
    protected $templateGlobalHelper;

    /**
     * @var array
     */
    protected $types;

    public function __construct(Repository $repository, GlobalHelper $gHelper)
    {
        $this->repository = $repository;
        $this->templateGlobalHelper = $gHelper;
    }

    public function addManagedType(Type $type, string $contentTypeIdentifier): void
    {
        $this->types[$contentTypeIdentifier] = $type;
    }

    /**
     * @return Type|false
     */
    public function getType(string $contentTypeIdentifier)
    {
        if (\is_array($this->types) && \array_key_exists($contentTypeIdentifier, $this->types)) {
            return $this->types[$contentTypeIdentifier];
        }

        return false;
    }

    /**
     * Inject variables in the view.
     */
    public function onPreContentView(PreContentViewEvent $event): void
    {
        $contentView = $event->getContentView();

        $location = $content = null;
        /**
         * @var ContentView $contentView
         */
        $viewType = $contentView->getViewType();
        if ($contentView instanceof ContentView) {
            $location = $contentView->getLocation();
            $content = $contentView->getContent();
        }

        if (\is_string($viewType) && $location instanceof Location) {
            if (1 === $location->invisible or 1 === $location->hidden) {
                throw new NotFoundHttpException('Page not found');
            }

            $identifier = $this->repository->getContentTypeService()->loadContentType(
                $location->contentInfo->contentTypeId
            )->identifier;

            if ($type = $this->getType($identifier)) {
                $type->setContentView($contentView);
                $type->setLocation($location);
                $type->setContent($content);

                $children = [];

                $method = 'get'.preg_replace_callback(
                    '/(?:^|_)(.?)/',
                    create_function('$matches', 'return strtoupper($matches[1]);'),
                    $viewType
                ).'Children';

                if (method_exists($type, $method)) {
                    $children = $type->$method(
                        $this->templateGlobalHelper->getViewParameters(),
                        $this->templateGlobalHelper->getSiteaccess()
                    );
                } elseif ('full' === $viewType && method_exists($type, 'getChildren')) {
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

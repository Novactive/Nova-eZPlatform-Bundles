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

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\Templating\GlobalHelper;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PreContentViewListener implements EventSubscriberInterface
{
    /**
     * @var array
     */
    protected array $types;

    public function __construct(protected Repository $repository, protected GlobalHelper $templateGlobalHelper)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MVCEvents::PRE_CONTENT_VIEW => 'onPreContentView',
        ];
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
        if (\array_key_exists($contentTypeIdentifier, $this->types)) {
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

            $type = $this->getType($identifier);

            if (false !== $type) {
                $type->setContentView($contentView);
                $type->setLocation($location);
                $type->setContent($content);

                $children = [];

                $method = 'get'.preg_replace_callback(
                        '/(?:^|_)(.?)/',
                        static fn(array $matches): string => strtolower($matches[0]),
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

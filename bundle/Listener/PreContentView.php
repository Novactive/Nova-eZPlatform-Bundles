<?php


namespace Novactive\Bundle\NovaeZEditHelpBundle\Listener;

use Novactive\Bundle\NovaeZEditHelpBundle\Services\FetchDocumentation;
use eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent;
use EzSystems\RepositoryForms\Content\View\ContentEditView;

class PreContentView
{
    protected $fetchDocumentation;

    public function __construct(FetchDocumentation $fetchDocumentation)
    {
        $this->fetchDocumentation = $fetchDocumentation;
    }

    public function onPreContentView(PreContentViewEvent $event)
    {
        if ($event->getContentView() instanceof ContentEditView) {
            $contentType = $event->getContentView()->getContent()->getContentType();
            $documentation = $this->fetchDocumentation->getByContentType($contentType);

            if (!empty($documentation)) {
                $mainLocationId = $documentation->valueObject->contentInfo->mainLocationId;
                $children = $this->fetchDocumentation->getChildrenByLocationId($mainLocationId);


                $items = [];
                if (!empty($children)) {
                    foreach ($children as $child) {
                        $identifier = (string) $child->valueObject->getFieldValue('identifier');
                        $items[$identifier] = $child->valueObject;
                    }
                }

                $event->getContentView()->addParameters(['items' => $items]);
            }
        }
    }

}
<?php

/**
 * NovaeZEditHelpBundle.
 *
 * @package   Novactive\Bundle\NovaeZEditHelpBundle
 *
 * @author    sergmike
 * @copyright 2019
 * @license   https://github.com/Novactive/NovaeZEditHelpBundle MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\NovaeZEditHelpBundle\Listener;

use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Ibexa\ContentForms\Content\View\ContentCreateView;
use Ibexa\ContentForms\Content\View\ContentEditView;
use Novactive\Bundle\NovaeZEditHelpBundle\Services\FetchDocumentation;

class PreContentView
{
    public function __construct(
        protected FetchDocumentation $fetchDocumentation
    ) {
    }

    public function onPreContentView(PreContentViewEvent $event): void
    {
        $contentView = $event->getContentView();
        if ($contentView instanceof ContentEditView || $contentView instanceof ContentCreateView) {
            $contentType = $contentView instanceof ContentEditView ? $contentView->getContent()->getContentType(
            ) : $contentView->getContentType();

            $documentation = $this->fetchDocumentation->getByContentType($contentType);

            if (null !== $documentation) {
                $mainLocationId = $documentation->contentInfo->mainLocationId;
                $children = $this->fetchDocumentation->getChildrenByLocationId($mainLocationId);
                $items = [];
                foreach ($children as $child) {
                    $identifier = (string) $child->valueObject->getFieldValue('identifier');
                    $items[$identifier] = $child->valueObject;
                }

                $template = $contentView->getTemplateIdentifier();
                $contentView->setTemplateIdentifier(str_replace('.html.twig', '_help.html.twig', $template));

                $event->getContentView()->addParameters(
                    [
                        'documentation' => $documentation,
                        'extend_from_template' => $template,
                        'documentation_items' => $items ?? [],
                    ]
                );
            }
        }
    }
}

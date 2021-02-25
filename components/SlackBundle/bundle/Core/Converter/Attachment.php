<?php

/**
 * NovaeZSlackBundle Bundle.
 *
 * @package   Novactive\Bundle\eZSlackBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZSlackBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZSlackBundle\Core\Converter;

use DateTime;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Content as ValueContent;
use eZ\Publish\Core\FieldType\Image\Value as ImageValue;
use eZ\Publish\Core\FieldType\RelationList\Value as RelationListValue;
use eZ\Publish\Core\FieldType\Relation\Value as RelationValue;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Novactive\Bundle\eZSlackBundle\Core\Decorator\Attachment as AttachmentDecorator;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Attachment as AttachmentModel;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Field;
use Novactive\Bundle\eZSlackBundle\Core\Slack\NewBuilder\Context;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackImageBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackImageBlockElement;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use EzSystems\EzPlatformRichText\eZ\RichText\Converter as RichTextConverter;
use EzSystems\EzPlatformRichText\eZ\FieldType\RichText\Value as RichTextValue;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @SuppressWarnings(PHPMD.IfStatementAssignment)
 */
class Attachment
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var RichTextConverter
     */
    private $richTextConverter;

    /**
     * @var array
     */
    private $siteAccessList;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var AttachmentDecorator
     */
    private $attachmentDecorator;

    private TranslatorInterface $translator;

    /**
     * Attachment constructor.
     */
    public function __construct(
        Repository $repository,
        RichTextConverter $converter,
        RouterInterface $router,
        ConfigResolverInterface $configResolver,
        AttachmentDecorator $decorator,
        TranslatorInterface $translator,
        array $siteAccessList
    ) {
        $this->repository = $repository;
        $this->richTextConverter = $converter;
        $this->siteAccessList = $siteAccessList;
        $this->router = $router;
        $this->attachmentDecorator = $decorator;
        $this->configResolver = $configResolver;
        $this->translator = $translator;
    }

    /**
     * @param $name
     */
    private function getParameter($name)
    {
        return $this->configResolver->getParameter($name, 'nova_ezslack');
    }

    private function findContent(int $id): Content
    {
        $contentService = $this->repository->getContentService();

        return $contentService->loadContent($id);
    }

    /**
     * @return Attachment[]
     */
    public function convert(int $contentId): array
    {
        $attachments = [
            $this->getMain($contentId),
            $this->getDetails($contentId),
        ];

        if ($attachment = $this->getPreview($contentId)) {
            $attachments[] = $attachment;
        }

        return $attachments;
    }

    public function getMain(int $contentId): AttachmentModel
    {
        $content = $this->findContent($contentId);
        $attachment = new AttachmentModel();
        $this->attachmentDecorator->addAuthor($attachment, $content->contentInfo->ownerId);
        $attachment->setTitle($content->contentInfo->name);
        $attachment->setText($this->getDescription($content));
        $mediaSection = $this->repository->sudo(
            function (Repository $repository) {
                return $repository->getSectionService()->loadSectionByIdentifier('media');
            }
        );
        if ($content->contentInfo->sectionId !== $mediaSection->id) {
            $attachment->setThumbURL($this->attachmentDecorator->getPictureUrl($content));
        }
        $this->attachmentDecorator->decorate($attachment);
        $this->attachmentDecorator->addSiteInformation($attachment);

        return $attachment;
    }

    public function getMainBlocks(int $contentId): array
    {
        $content = $this->findContent($contentId);

        $blocks = [];

        $owner = $this->findContent($content->contentInfo->ownerId);
        $ownerPicture = $this->getPicture($owner);
        $ownerBlock = new Context();
        if (null !== $ownerPicture) {
            $ownerBlock->image($ownerPicture['uri'], $ownerPicture['alternativeText']);
        }
        $ownerBlock->text($this->sanitize($owner->contentInfo->name), 'plain_text');
        $blocks[] = $ownerBlock;

        $textFields = $this->sanitize('*'.$content->contentInfo->name.'*');
        $description = $this->getDescription($content);
        if (null !== $description && !empty($description)) {
            $textFields .= "\n\n".$this->sanitize($description);
        }
        $contentBlock = (new SlackSectionBlock())->text($textFields);
        $contentImage = $this->getPicture($content);
        if (null !== $contentImage) {
            $contentBlock->accessory(
                new SlackImageBlockElement($contentImage['uri'], $contentImage['alternativeText'])
            );
        }
        $blocks[] = $contentBlock;

        $siteName = $this->getParameter('site_name');
        if (null !== $siteName) {
            $siteInfoBlock = new Context();
            $siteFavicon = $this->getParameter('favicon');
            if (null !== $siteFavicon) {
                $siteInfoBlock->image($siteFavicon, $siteName);
            }
            $siteInfoBlock->text($siteName);
            $blocks[] = $siteInfoBlock;
        }

        $blocks[] = new SlackDividerBlock();

        return $blocks;
    }

    public function getDetailsBlock(int $contentId): array
    {
        $content = $this->findContent($contentId);
        $leftColumn = $rightColumn = '';
        if (null !== $content->contentInfo->publishedDate) {
            $leftColumn .= '*'.$this->translator->trans('field.content.published', [], 'slack')."*\n".
                           $this->formatDate($content->contentInfo->publishedDate);
        }
        if (!empty($leftColumn)) {
            $leftColumn .= "\n\n";
        }
        $leftColumn .= '*'.$this->translator->trans('field.content.id', [], 'slack')."*\n".$content->id;
        if ($content->contentInfo->mainLocationId > 0) {
            $leftColumn .= "\n\n*".$this->translator->trans('field.content.mainlocationid', [], 'slack')."*\n".
                           $content->contentInfo->mainLocationId;
        }
        $objectStateService = $this->repository->getObjectStateService();
        $allGroups = $objectStateService->loadObjectStateGroups();
        foreach ($allGroups as $group) {
            if ('ez_lock' === $group->identifier) {
                continue;
            }
            $state = $this->repository->getObjectStateService()->getContentState($content->contentInfo, $group);
            $leftColumn .= "\n\n*".$group->getName($group->mainLanguageCode)."*\n".
                           $state->getName($state->mainLanguageCode);
        }
        if ($content->contentInfo->published) {
            $locations = $this->repository->getLocationService()->loadLocations($content->contentInfo);
            foreach ($locations as $location) {
                foreach ($this->siteAccessList as $siteAccessName) {
                    $url = $this->router->generate(
                        '_ez_content_view',
                        [
                            'contentId' => $location->contentId,
                            'locationId' => $location->id,
                            'siteaccess' => $siteAccessName
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );
                    $fieldName = "SiteAccess {$siteAccessName}";
                    if ($location->id !== $content->contentInfo->mainLocationId) {
                        $fieldName = "Location: {$location->id} {$fieldName}";
                    }
                    $leftColumn .= "\n\n*".$fieldName."*\n".explode('?', $url)[0];
                }
            }
        }

        if (null !== $content->contentInfo->modificationDate) {
            $rightColumn .= '*'.$this->translator->trans('field.content.modified', [], 'slack')."*\n".
                            $this->formatDate($content->contentInfo->modificationDate);
        }
        if (!empty($rightColumn)) {
            $rightColumn .= "\n\n";
        }
        $rightColumn .= '*'.$this->translator->trans('field.content.version', [], 'slack')."*\n".
                        $content->contentInfo->currentVersionNo;
        $rightColumn .= "\n\n*".$this->translator->trans('field.content.languages', [], 'slack')."*\n".
                        implode(',', $content->versionInfo->languageCodes);

        return [
            (new SlackSectionBlock())->field($leftColumn)->field($rightColumn),
            new SlackDividerBlock()
        ];
    }

    public function getDetails(int $contentId): AttachmentModel
    {
        $content = $this->findContent($contentId);
        $attachment = new AttachmentModel();
        $fields = [];
        if (null !== $content->contentInfo->publishedDate) {
            $fields[] = new Field(
                '_t:field.content.published',
                $this->formatDate($content->contentInfo->publishedDate)
            );
        }
        if (null !== $content->contentInfo->modificationDate) {
            $fields[] = new Field(
                '_t:field.content.modified',
                $this->formatDate($content->contentInfo->modificationDate)
            );
        }

        $fields[] = new Field('_t:field.content.id', (string) $content->id);
        $fields[] = new Field(
            '_t:field.content.version',
            (string) $content->contentInfo->currentVersionNo
        );

        if ($content->contentInfo->mainLocationId > 0) {
            $fields[] = new Field(
                '_t:field.content.mainlocationid',
                (string) $content->contentInfo->mainLocationId
            );
        }
        $fields[] = new Field(
            '_t:field.content.languages',
            implode(',', $content->versionInfo->languageCodes)
        );

        // states
        $objectStateService = $this->repository->getObjectStateService();
        $allGroups = $objectStateService->loadObjectStateGroups();
        foreach ($allGroups as $group) {
            if ('ez_lock' === $group->identifier) {
                continue;
            }
            $state = $this->repository->getObjectStateService()->getContentState($content->contentInfo, $group);
            $fields[] = new Field($group->getName($group->mainLanguageCode), $state->getName($state->mainLanguageCode));
        }

        if ($content->contentInfo->published) {
            $locations = $this->repository->getLocationService()->loadLocations($content->contentInfo);
            foreach ($locations as $location) {
                foreach ($this->siteAccessList as $siteAccessName) {
                    $url = $this->router->generate(
                        '_ez_content_view',
                        [
                            'contentId' => $location->contentId,
                            'locationId' => $location->id,
                            'siteaccess' => $siteAccessName
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );
                    $fieldName = "SiteAccess {$siteAccessName}";
                    if ($location->id !== $content->contentInfo->mainLocationId) {
                        $fieldName = "Location: {$location->id} {$fieldName}";
                    }
                    $fields[] = new Field($fieldName, explode('?', $url)[0], false);
                }
            }
        }
        $attachment->setFields($fields);
        $this->attachmentDecorator->decorate($attachment, 'details');

        return $attachment;
    }

    public function getPreview(int $contentId): ?AttachmentModel
    {
        $content = $this->findContent($contentId);
        $mediaSection = $this->repository->sudo(
            function (Repository $repository) {
                return $repository->getSectionService()->loadSectionByIdentifier('media');
            }
        );
        if ($content->contentInfo->sectionId === $mediaSection->id) {
            $attachment = new AttachmentModel();
            $attachment->setImageURL($this->attachmentDecorator->getPictureUrl($content));
            $this->attachmentDecorator->decorate($attachment, 'preview');

            return $attachment;
        }

        return null;
    }

    public function getPreviewBlock(int $contentId): array
    {
        $content = $this->findContent($contentId);
        $mediaSection = $this->repository->sudo(
            function (Repository $repository) {
                return $repository->getSectionService()->loadSectionByIdentifier('media');
            }
        );
        $blocks = [];
        if ($content->contentInfo->sectionId === $mediaSection->id) {
            $contentImage = $this->getPicture($content);
            if (null !== $contentImage) {
                $blocks[] = new SlackImageBlock($contentImage['uri'], $contentImage['alternativeText']);
                $blocks[] = new SlackDividerBlock();
            }
        }

        return $blocks;
    }

    private function getDescription(ValueContent $content): ?string
    {
        $fieldIdentifiers = $this->getParameter('field_identifiers')['description'];
        foreach ($fieldIdentifiers as $try) {
            $value = $content->getFieldValue($try);
            if (null === $value) {
                continue;
            }
            if ($value instanceof RichTextValue) {
                return $this->richTextConverter->convert($value->xml)->saveHTML();
            }
            if (isset($value->text)) {
                return $value->text;
            }
        }

        return null;
    }

    private function formatDate(DateTime $dateTime): string
    {
        return $dateTime->format(DateTime::RFC850);
    }

    private function sanitize(?string $text): ?string
    {
        if (null === $text) {
            return null;
        }

        return trim(strip_tags(html_entity_decode($text)));
    }

    private function getPicture(ValueContent $content): ?array
    {
        $fieldIdentifiers = $this->getParameter('field_identifiers')['image'];
        foreach ($fieldIdentifiers as $try) {
            $value = $content->getFieldValue($try);
            if ($value instanceof ImageValue) {
                return [
                    'uri' => ($this->getParameter('asset_prefix') ?? '').$value->uri,
                    'alternativeText' => $value->alternativeText
                ];
            }
            if ($value instanceof RelationValue && $value->destinationContentId > 0) {
                $image = $this->findContent($value->destinationContentId);

                return $this->getPicture($image);
            }
            if ($value instanceof RelationListValue && \count($value->destinationContentIds) > 0) {
                $image = $this->findContent($value->destinationContentIds[0]);

                return $this->getPicture($image);
            }
        }

        return null;
    }
}

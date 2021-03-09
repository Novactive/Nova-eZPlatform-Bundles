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
use eZ\Publish\Core\FieldType\Relation\Value as RelationValue;
use eZ\Publish\Core\FieldType\RelationList\Value as RelationListValue;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformRichText\eZ\FieldType\RichText\Value as RichTextValue;
use EzSystems\EzPlatformRichText\eZ\RichText\Converter as RichTextConverter;
use Novactive\Bundle\eZSlackBundle\Core\Slack\SlackBlock\Context;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackImageBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackImageBlockElement;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @SuppressWarnings(PHPMD.IfStatementAssignment)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class Attachment
{
    private Repository $repository;

    private RichTextConverter $richTextConverter;

    private array $siteAccessList;

    private RouterInterface $router;

    private ConfigResolverInterface $configResolver;

    private TranslatorInterface $translator;

    public function __construct(
        Repository $repository,
        RichTextConverter $converter,
        RouterInterface $router,
        ConfigResolverInterface $configResolver,
        TranslatorInterface $translator,
        array $siteAccessList
    ) {
        $this->repository = $repository;
        $this->richTextConverter = $converter;
        $this->siteAccessList = $siteAccessList;
        $this->router = $router;
        $this->configResolver = $configResolver;
        $this->translator = $translator;
    }

    private function getParameter($name)
    {
        return $this->configResolver->getParameter($name, 'nova_ezslack');
    }

    private function findContent(int $id): Content
    {
        $contentService = $this->repository->getContentService();

        return $contentService->loadContent($id);
    }

    public function convert(int $contentId): array
    {
        $attachments = [
            $this->getMainBlocks($contentId),
            $this->getDetailsBlock($contentId),
        ];

        if ($attachment = $this->getPreviewBlock($contentId)) {
            $attachments[] = $attachment;
        }

        return $attachments;
    }

    public function getMainBlocks(int $contentId): array
    {
        $content = $this->findContent($contentId);

        $blocks = [];

        $ownerBlock = new Context();
        try {
            $owner = $this->findContent($content->contentInfo->ownerId);
            $ownerPicture = $this->getPicture($owner);
            $ownerName = $this->sanitize($owner->contentInfo->name);
        } catch (\Exception $e) {
            $ownerPicture = null;
            $ownerName = $e->getMessage();
        }
        if (null !== $ownerPicture) {
            $ownerBlock->image($ownerPicture['uri'], (string) $ownerPicture['alternativeText']);
        }
        $ownerBlock->text($ownerName, 'plain_text');
        $blocks[] = $ownerBlock;

        $textFields = $this->sanitize('*'.$content->contentInfo->name.'*');
        $description = $this->getDescription($content);
        if (null !== $description && !empty($description)) {
            $textFields .= "\n\n".$this->sanitize($description);
        }

        $contentBlock = (new SlackSectionBlock())->text($textFields);
        try {
            $contentImage = $this->getPicture($content);
        } catch (\Exception $e) {
            $contentImage = null;
        }
        if (null !== $contentImage) {
            $contentBlock->accessory(
                new SlackImageBlockElement($contentImage['uri'], (string) $contentImage['alternativeText'])
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

        $blocks = [
            (new SlackSectionBlock())->field($leftColumn)->field($rightColumn),
        ];

        if ($content->contentInfo->published) {
            $siteAccessLinks = '';
            $locations = $this->repository->getLocationService()->loadLocations($content->contentInfo);
            foreach ($locations as $location) {
                foreach ($this->siteAccessList as $siteAccessName) {
                    $url = $this->router->generate(
                        '_ez_content_view',
                        [
                            'contentId' => $location->contentId,
                            'locationId' => $location->id,
                            'siteaccess' => $siteAccessName,
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );
                    $fieldName = "SiteAccess {$siteAccessName}";
                    if ($location->id !== $content->contentInfo->mainLocationId) {
                        $fieldName = "Location: {$location->id} {$fieldName}";
                    }
                    $siteAccessLinks .= "\n\n*".$fieldName."*\n".explode('?', $url)[0];
                }
            }
            $blocks[] = (new SlackSectionBlock())->text($siteAccessLinks);
        }
        $blocks[] = new SlackDividerBlock();

        return $blocks;
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
                    'alternativeText' => $value->alternativeText,
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

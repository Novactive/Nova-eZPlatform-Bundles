<?php

/**
 * NovaeZRssFeedBundle.
 *
 * @package   NovaeZRssFeedBundle
 *
 * @author    Novactive
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZRssFeedBundle/blob/master/LICENSE
 */

namespace Novactive\EzRssFeedBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * RssFeedsItems.
 *
 * @ORM\Table(name="rss_feed_items")
 * @ORM\Entity(repositoryClass="Novactive\EzRssFeedBundle\Repository\RssFeedsItemsRepository")
 */
class RssFeedItems
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="include_subtree", type="boolean")
     */
    private $includeSubtree;

    /**
     * @var int
     *
     * @ORM\Column(name="contenttype_id", type="integer")
     */
    private $contentTypeId;

    /**
     * @var DateTime
     * @ORM\Column(name="modified_at", type="datetime", nullable=true)
     */
    private $modifiedAt;

    /**
     * @var DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="subtree_path", type="integer")
     */
    private $subtreePath;

    /**
     * @var bool
     *
     * @ORM\Column(name="only_visible", type="boolean")
     */
    private $onlyVisible;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", nullable=true)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="media", type="string", nullable=true)
     */
    private $media;

    /**
     * @ORM\ManyToOne(targetEntity="Novactive\EzRssFeedBundle\Entity\RssFeeds",
     *     inversedBy="feedItems",
     *     cascade={"persist"})
     * @ORM\JoinColumn(name="rss_feeds_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     */
    private $rssFeeds;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->modifiedAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getModifiedAt(): ?DateTime
    {
        return $this->modifiedAt;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setModifiedAt(DateTime $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    public function getRssFeeds(): ?RssFeeds
    {
        return $this->rssFeeds;
    }

    public function setRssFeeds(RssFeeds $rssFeeds = null): self
    {
        $this->rssFeeds = $rssFeeds;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'locationId' => $this->getSubtreePath(),
            'includeSubtreePath' => $this->getIncludeSubtree(),
            'onlyVisible' => $this->getOnlyVisible(),
            'contentTypeId' => $this->getContentTypeId(),
            'fieldTypesIdentifier' => [
                'title' => $this->getTitle(),
                'description' => $this->getDescription(),
                'category' => $this->getCategory(),
                'media' => $this->getMedia(),
            ],
        ];
    }

    public function getSubtreePath(): int
    {
        return $this->subtreePath;
    }

    public function setSubtreePath($subtreePath): self
    {
        $this->subtreePath = $subtreePath;

        return $this;
    }

    public function getIncludeSubtree(): bool
    {
        return $this->includeSubtree;
    }

    public function setIncludeSubtree($includeSubtree): self
    {
        $this->includeSubtree = $includeSubtree;

        return $this;
    }

    public function getContentTypeId(): ?int
    {
        return $this->contentTypeId;
    }

    public function setContentTypeId($contentTypeId): self
    {
        $this->contentTypeId = $contentTypeId;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title ?? '';
    }

    public function setTitle(?string $title): self
    {
        $this->title = (string) $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription($description = null): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory($category = null): self
    {
        $this->category = $category;

        return $this;
    }

    public function getMedia(): ?string
    {
        return $this->media;
    }

    public function setMedia($media = null): self
    {
        $this->media = $media;

        return $this;
    }

    public function getOnlyVisible(): bool
    {
        return $this->onlyVisible;
    }

    public function setOnlyVisible(bool $onlyVisible): void
    {
        $this->onlyVisible = $onlyVisible;
    }
}

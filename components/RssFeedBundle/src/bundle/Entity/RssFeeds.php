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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * RssFeeds.
 *
 * @ORM\Table(name="rss_feeds")
 * @ORM\Entity(repositoryClass="Novactive\EzRssFeedBundle\Repository\RssFeedsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class RssFeeds
{
    public const STATUS_ENABLED = 1;

    public const STATUS_DISABLED = 0;

    public const SORT_TYPE_PUBLICATION = 1;

    public const SORT_TYPE_MODIFICATION = 2;

    public const SORT_TYPE_NAME = 3;

    public const SORT_DIRECTION_ASC = 'ascending';

    public const SORT_DIRECTION_DESC = 'descending';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     */
    private string $title;

    /**
     * @ORM\Column(name="description", type="string", length=255)
     */
    private string $description;

    /**
     * @ORM\Column(name="url_slug", type="string", length=255, unique=true)
     */
    private string $urlSlug;
    /**
     * @ORM\OneToMany(targetEntity="Novactive\EzRssFeedBundle\Entity\RssFeedSite",
     *     mappedBy="rssFeeds",
     *     cascade={"all"})
     */
    private Collection $feedSites;

    /**
     * @ORM\Column(name="status", type="integer")
     */
    private int $status;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(name="number_of_object", type="integer")
     */
    private int $numberOfObject;

    /**
     * @ORM\Column(name="sort_type", type="integer")
     */
    private int $sortType;

    /**
     * @ORM\Column(name="sort_direction", type="string")
     */
    private string $sortDirection;

    /**
     * @ORM\Column(name="modified_at", type="datetime", nullable=true)
     */
    private DateTime $modifiedAt;

    /**
     * @ORM\OneToMany(targetEntity="Novactive\EzRssFeedBundle\Entity\RssFeedItems",
     *     mappedBy="rssFeeds",
     *     cascade={"all"})
     */
    private Collection $feedItems;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->feedItems = new ArrayCollection();
        $this->feedSites = new ArrayCollection();
        $this->sortType = self::SORT_TYPE_PUBLICATION;
        $this->sortDirection = self::SORT_DIRECTION_DESC;
        $this->numberOfObject = 10;
        $this->createdAt = new DateTime();
        $this->modifiedAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUrlSlug(): string
    {
        return $this->urlSlug;
    }

    public function setUrlSlug($urlSlug): self
    {
        $this->urlSlug = $urlSlug;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getModifiedAt(): ?DateTime
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(DateTime $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    public function getNumberOfObject(): int
    {
        return $this->numberOfObject;
    }

    public function setNumberOfObject(int $numberOfObject): self
    {
        $this->numberOfObject = $numberOfObject;

        return $this;
    }

    public function addFeedItem(RssFeedItems $feedItem): self
    {
        $this->feedItems[] = $feedItem;
        $feedItem->setRssFeeds($this);

        return $this;
    }

    /**
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeFeedItem(RssFeedItems $feedItem): bool
    {
        return $this->feedItems->removeElement($feedItem);
    }

    public function getFeedItems(): Collection
    {
        return $this->feedItems;
    }

    public function getSortType(): int
    {
        return $this->sortType;
    }

    public function setSortType(int $sortType): self
    {
        $this->sortType = $sortType;

        return $this;
    }

    public function getSortDirection(): string
    {
        return $this->sortDirection;
    }

    public function setSortDirection(string $sortDirection): self
    {
        $this->sortDirection = $sortDirection;

        return $this;
    }

    public function addFeedSite(RssFeedSite $feedSite): self
    {
        $this->feedSites[] = $feedSite;
        $feedSite->setRssFeeds($this);

        return $this;
    }

    public function removeFeedSite(RssFeedSite $feedSite): bool
    {
        return $this->feedSites->removeElement($feedSite);
    }

    public function getFeedSites(): Collection
    {
        return $this->feedSites;
    }
}

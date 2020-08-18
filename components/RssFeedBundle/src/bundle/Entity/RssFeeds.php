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
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="url_slug", type="string", length=255, unique=true)
     */
    private $urlSlug;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var int
     * @ORM\Column(name="number_of_object", type="integer")
     */
    private $numberOfObject;

    /**
     * @var int
     * @ORM\Column(name="sort_type", type="integer")
     */
    private $sortType;

    /**
     * @var string
     * @ORM\Column(name="sort_direction", type="string")
     */
    private $sortDirection;

    /**
     * @var DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="modified_at", type="datetime", nullable=true)
     */
    private $modifiedAt;

    /**
     * @ORM\OneToMany(targetEntity="Novactive\EzRssFeedBundle\Entity\RssFeedItems",
     *     mappedBy="rssFeeds",
     *     cascade={"all"})
     */
    private $feedItems;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->feedItems = new ArrayCollection();
        $this->sortType = self::SORT_TYPE_PUBLICATION;
        $this->sortDirection = self::SORT_DIRECTION_DESC;
        $this->numberOfObject = 10;
        $this->createdAt = new DateTime();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return RssFeeds
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return RssFeeds
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get urlSlug.
     *
     * @return string
     */
    public function getUrlSlug()
    {
        return $this->urlSlug;
    }

    /**
     * Set urlSlug.
     *
     * @param string $urlSlug
     *
     * @return RssFeeds
     */
    public function setUrlSlug($urlSlug)
    {
        $this->urlSlug = $urlSlug;

        return $this;
    }

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status.
     *
     * @param int $status
     *
     * @return RssFeeds
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get modifiedAt.
     *
     * @return DateTime|null
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    public function getNumberOfObject(): int
    {
        return $this->numberOfObject;
    }

    public function setNumberOfObject(int $numberOfObject): void
    {
        $this->numberOfObject = $numberOfObject;
    }

    /**
     * Add feedItem.
     *
     * @return RssFeeds
     */
    public function addFeedItem(\Novactive\EzRssFeedBundle\Entity\RssFeedItems $feedItem)
    {
        $this->feedItems[] = $feedItem;
        $feedItem->setRssFeeds($this);

        return $this;
    }

    /**
     * Remove feedItem.
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeFeedItem(\Novactive\EzRssFeedBundle\Entity\RssFeedItems $feedItem)
    {
        return $this->feedItems->removeElement($feedItem);
    }

    /**
     * Get feedItems.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFeedItems()
    {
        return $this->feedItems;
    }

    public function getSortType(): int
    {
        return $this->sortType;
    }

    public function setSortType(int $sortType): void
    {
        $this->sortType = $sortType;
    }

    /**
     * @return int
     */
    public function getSortDirection(): string
    {
        return $this->sortDirection;
    }

    public function setSortDirection(string $sortDirection): void
    {
        $this->sortDirection = $sortDirection;
    }
}

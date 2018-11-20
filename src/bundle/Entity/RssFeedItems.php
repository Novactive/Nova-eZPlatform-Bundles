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

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="modified_at", type="datetime", nullable=true)
     */
    private $modifiedAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
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
     * RssFeedItems constructor.
     */
    public function __construct()
    {
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
     * Get modifiedAt.
     *
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get rssFeeds.
     *
     * @return \Novactive\EzRssFeedBundle\Entity\RssFeeds|null
     */
    public function getRssFeeds()
    {
        return $this->rssFeeds;
    }

    /**
     * Set rssFeeds.
     *
     * @param \Novactive\EzRssFeedBundle\Entity\RssFeeds|null $rssFeeds
     *
     * @return RssFeedItems
     */
    public function setRssFeeds(\Novactive\EzRssFeedBundle\Entity\RssFeeds $rssFeeds = null)
    {
        $this->rssFeeds = $rssFeeds;

        return $this;
    }

    public function toArray()
    {
        return [
            'locationId'           => $this->getSubtreePath(),
            'includeSubtreePath'   => $this->getIncludeSubtree(),
            'contentTypeId'        => $this->getContentTypeId(),
            'fieldTypesIdentifier' => [
                'title'       => $this->getTitle(),
                'description' => $this->getDescription(),
                'category'    => $this->getCategory(),
                'media'       => $this->getMedia(),
            ],
        ];
    }

    /**
     * Get subtreePath.
     *
     * @return int
     */
    public function getSubtreePath()
    {
        return $this->subtreePath;
    }

    /**
     * Set subtreePath.
     *
     * @param int $subtreePath
     *
     * @return RssFeedItems
     */
    public function setSubtreePath($subtreePath)
    {
        $this->subtreePath = $subtreePath;

        return $this;
    }

    /**
     * Get includeSubtree.
     *
     * @return bool
     */
    public function getIncludeSubtree()
    {
        return $this->includeSubtree;
    }

    /**
     * Set includeSubtree.
     *
     * @param bool $includeSubtree
     *
     * @return RssFeedItems
     */
    public function setIncludeSubtree($includeSubtree)
    {
        $this->includeSubtree = $includeSubtree;

        return $this;
    }

    /**
     * Get contentTypeId.
     *
     * @return int
     */
    public function getContentTypeId()
    {
        return $this->contentTypeId;
    }

    /**
     * Set contentTypeId.
     *
     * @param int $contentTypeId
     *
     * @return RssFeedItems
     */
    public function setContentTypeId($contentTypeId)
    {
        $this->contentTypeId = $contentTypeId;

        return $this;
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
     * @return RssFeedItems
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return RssFeedItems
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get category.
     *
     * @return string|null
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set category.
     *
     * @param string|null $category
     *
     * @return RssFeedItems
     */
    public function setCategory($category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get media.
     *
     * @return string|null
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Set media.
     *
     * @param string|null $media
     *
     * @return RssFeedItems
     */
    public function setMedia($media = null)
    {
        $this->media = $media;

        return $this;
    }
}

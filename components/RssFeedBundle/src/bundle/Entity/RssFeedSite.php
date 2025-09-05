<?php

namespace Novactive\EzRssFeedBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RssFeeds.
 *
 * @ORM\Table(name="rss_feed_sites")
 *
 * @ORM\Entity(repositoryClass="Novactive\EzRssFeedBundle\Repository\RssFeedSitesRepository")
 *
 * @ORM\HasLifecycleCallbacks()
 */
class RssFeedSite
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     *
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="identifier", type="string", length=255)
     */
    private $identifier;
    /**
     * @ORM\ManyToOne(targetEntity="Novactive\EzRssFeedBundle\Entity\RssFeeds",
     *     inversedBy="feedSites",
     *     cascade={"persist"})
     *
     * @ORM\JoinColumn(name="rss_feeds_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     */
    private $rssFeeds;

    public function getId(): int
    {
        return $this->id;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getRssFeeds(): ?RssFeeds
    {
        return $this->rssFeeds;
    }

    public function setRssFeeds(?RssFeeds $rssFeeds = null): self
    {
        $this->rssFeeds = $rssFeeds;

        return $this;
    }
}

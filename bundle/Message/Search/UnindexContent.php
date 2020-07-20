<?php

/**
 * Nova eZ Accelerator.
 *
 * @package   Novactive\Bundle\eZAccelerator
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @author    Sébastien Morel (Plopix) <morel.seb@gmail.com>
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZAccelerator/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAccelerator\Message\Search;

use Novactive\Bundle\eZAccelerator\Contracts\SiteAccessAware;
use Novactive\Bundle\eZAccelerator\Contracts\SiteAccessAwareInterface;

final class UnindexContent implements SiteAccessAwareInterface
{
    use SiteAccessAware;

    /**
     * @var int
     */
    private $contentId;

    /**
     * @var int|null
     */
    private $versionId;

    public function __construct(int $contentId, ?int $versionId = null)
    {
        $this->contentId = $contentId;
        $this->versionId = $versionId;
    }

    public function getContentId(): int
    {
        return $this->contentId;
    }

    public function getVersionId(): ?int
    {
        return $this->versionId;
    }
}

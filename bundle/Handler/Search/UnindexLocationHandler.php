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

namespace Novactive\Bundle\eZAccelerator\Handler\Search;

use eZ\Publish\SPI\Search\Handler;
use Novactive\Bundle\eZAccelerator\Message\Search\UnindexLocation;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UnindexLocationHandler implements MessageHandlerInterface
{
    /**
     * @var Handler
     */
    private $searchHandler;

    public function __construct(Handler $searchHandler)
    {
        $this->searchHandler = $searchHandler;
    }

    public function __invoke(UnindexLocation $message)
    {
        $this->searchHandler->deleteLocation($message->getLocationId(), $message->getContentId());
    }
}

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

use eZ\Publish\SPI\Persistence\Handler as PersistenceHandler;
use eZ\Publish\SPI\Search\Handler;
use Novactive\Bundle\eZAccelerator\Message\Search\IndexContent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class IndexContentHandler implements MessageHandlerInterface
{
    /**
     * @var Handler
     */
    private $searchHandler;

    /**
     * @var PersistenceHandler
     */
    private $persistenceHandler;

    public function __construct(Handler $searchHandler, PersistenceHandler $persistenceHandler)
    {
        $this->searchHandler      = $searchHandler;
        $this->persistenceHandler = $persistenceHandler;
    }

    public function __invoke(IndexContent $message): void
    {
        $this->searchHandler->indexContent(
            $this->persistenceHandler->contentHandler()->load($message->getContentId())
        );
    }
}

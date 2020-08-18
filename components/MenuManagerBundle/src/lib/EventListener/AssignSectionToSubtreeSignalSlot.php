<?php

/**
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    florian
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Novactive\EzMenuManager\EventListener;

use Doctrine\DBAL\Connection;
use eZ\Publish\Core\Persistence\Legacy\Content\Location\Handler;
use eZ\Publish\Core\SignalSlot\Signal;
use eZ\Publish\Core\SignalSlot\Slot;
use PDO;

class AssignSectionToSubtreeSignalSlot extends Slot
{
    use CachePurgerTrait;

    /** @var Connection */
    protected $connection;

    /** @var Handler */
    protected $locationHandler;

    /**
     * AssignSectionToSubtreeSignalSlot constructor.
     */
    public function __construct(Connection $connection, Handler $locationHandler)
    {
        $this->connection = $connection;
        $this->locationHandler = $locationHandler;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function receive(Signal $signal): void
    {
        if (!$signal instanceof Signal\SectionService\AssignSectionToSubtreeSignal) {
            return;
        }

        $loadedSubtree = $this->locationHandler->load($signal->locationId);
        $selectContentIdsQuery = $this->connection->createQueryBuilder();
        $selectContentIdsQuery
            ->select('t.contentobject_id')
            ->from('ezcontentobject_tree', 't')
            ->where(
                $selectContentIdsQuery->expr()->like(
                    't.path_string',
                    $selectContentIdsQuery->createPositionalParameter("{$loadedSubtree->pathString}%")
                )
            );

        $contentIds = array_map(
            'intval',
            $selectContentIdsQuery->execute()->fetchAll(PDO::FETCH_COLUMN)
        );

        foreach ($contentIds as $contentId) {
            $this->purgeContentMenuItemCache($contentId);
        }
    }
}

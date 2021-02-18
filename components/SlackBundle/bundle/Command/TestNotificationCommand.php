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

namespace Novactive\Bundle\eZSlackBundle\Command;

use Novactive\Bundle\eZSlackBundle\Core\Dispatcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Events\Content\PublishVersionEvent;

class TestNotificationCommand extends Command
{
    private Dispatcher $dispatcher;

    private Repository $repository;

    public function __construct(Dispatcher $dispatcher, Repository $repository)
    {
        $this->dispatcher = $dispatcher;
        $this->repository = $repository;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('novaezslack:test:notification:message')
            ->setDescription('Convert a Content and send the notification in the channel(s).')
            ->setHidden(true)
            ->addArgument('contentId', InputArgument::OPTIONAL, 'ContentId', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $contentId = (int) $input->getArgument('contentId');
        $content = $this->repository->getContentService()->loadContent($contentId);
        $event = new PublishVersionEvent($content, $content->getVersionInfo(), []);
        $this->dispatcher->receive($event);
        $output->writeln("Dispatch {$contentId} Done.");
    }
}

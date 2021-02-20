<?php

namespace Novactive\Bundle\eZSlackBundle\Command;

use Novactive\Bundle\eZSlackBundle\Core\Slack\NewBuilder\BlockElement\StaticSelect;
use Novactive\Bundle\eZSlackBundle\Core\Slack\NewBuilder\Header;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Novactive\Bundle\eZSlackBundle\Core\Slack\NewBuilder\Action;
use Novactive\Bundle\eZSlackBundle\Core\Slack\NewBuilder\Section;
use Novactive\Bundle\eZSlackBundle\Core\Client\Slack;

class NewNotificationCommand extends Command
{
    protected static $defaultName = 'nova:ez:slack:new:notification';

    private ChatterInterface $chatter;

    private Slack $slackClient;

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Send Slack Notification.');
    }

    public function __construct(ChatterInterface $chatter, Slack $slackClient)
    {
        $this->chatter = $chatter;
        $this->slackClient = $slackClient;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Sending...');

        //$chatMessage = new ChatMessage('Interactive');

        $actionBlock = (new Action())
            ->button(
                'Approve',
                'approve',
                'click_approve',
                'primary'
            );

        $titleBlock = (new Section(uniqid(date('Y-m-d H:i:s').' - ', false)))->blockId('title');

        $slackOptions = (new SlackOptions())
            ->block((new SlackSectionBlock())->text(uniqid(date('Y-m-d H:i:s').' - ', false)))
            ->block($titleBlock);
//            ->block($actionBlock)
//            ->block(new SlackDividerBlock())
//            ->block(
//                (new SlackSectionBlock())
//                    ->text('DropDown')
//                    ->accessory(
//                        new StaticSelect(
//                            'select_option', 'Select Option',
//                            [
//                                'Green' => 'green',
//                                'Blue' => 'blue',
//                                'Yellow' => 'yellow'
//                            ]
//                        )
//                    )
//            );

        //dd($slackOptions->toArray());

        dd(array_column($slackOptions->toArray()['blocks'], 'block_id'));

        $this->slackClient->sendMessage($slackOptions);

//        $chatMessage->options($slackOptions);
//        $chatMessage->transport('slack2');
//
//        $this->chatter->send($chatMessage);

        $io->success('Done');

        return 0;
    }
}
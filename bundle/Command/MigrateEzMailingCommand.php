<?php
/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <m.strukov@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Novactive\Bundle\eZMailingBundle\Entity\Campaign;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use Novactive\Bundle\eZMailingBundle\Entity\MailingList;
use Novactive\Bundle\eZMailingBundle\Entity\Registration;
use Novactive\Bundle\eZMailingBundle\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Novactive\Bundle\eZMailingBundle\Core\IOService;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\MVC\ConfigResolverInterface;

/**
 * Class MigrateEzMailingCommand.
 */
class MigrateEzMailingCommand extends Command
{
    /**
     * @var IOService
     */
    private $ioService;

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Repository;
     */
    private $ezRepository;

    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    public const DEFAULT_FALLBACK_LOCATION_ID = 2;

    public const DUMP_FOLDER = 'migrate/ezmailing';

    /**
     * MigrateCommand constructor.
     */
    public function __construct(
        IOService $ioService,
        EntityManagerInterface $entityManager,
        Repository $ezRepository,
        ConfigResolverInterface $configResolver
    ) {
        parent::__construct();
        $this->ioService      = $ioService;
        $this->entityManager  = $entityManager;
        $this->ezRepository   = $ezRepository;
        $this->configResolver = $configResolver;
    }

    protected function configure(): void
    {
        $this
            ->setName('novaezmailing:migrate:ezmailing')
            ->setDescription('Import database from the old one.')
            ->addOption('export', null, InputOption::VALUE_NONE, 'Export from old DB to json files')
            ->addOption('import', null, InputOption::VALUE_NONE, 'Import from json files to new DB')
            ->addOption('clean', null, InputOption::VALUE_NONE, 'Clean the existing data')
            ->setHelp('Run novaezmailing:migrate:ezmailing --export|--import|--clean');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title('Update the Database with Custom Novactive EzMailing Tables');

        if ($input->getOption('export')) {
            $this->export();
        } elseif ($input->getOption('import')) {
            $this->import();
        } elseif ($input->getOption('clean')) {
            $this->clean();
        } else {
            $this->io->error('No export or import option found. Run novaezmailing:migrate:ezmailing --export|--import');
        }
    }

    private function export(): void
    {
        // clean the 'ezmailing' dir
        $this->ioService->cleanDir(self::DUMP_FOLDER);
        $this->io->section('Cleaned the folder with json files.');
        $this->io->section('Exporting from old database to json files.');

        // Get the Lists first, then Users and subscriptions (which are supposed to be registrations)
        $contentService         = $this->ezRepository->getContentService();
        $contentLanguageService = $this->ezRepository->getContentLanguageService();
        $languages              = $contentLanguageService->loadLanguages();
        $defaultLanguageCode    = $contentLanguageService->getDefaultLanguageCode();
        $siteAccessList         = $this->configResolver->getParameter('list', 'ezpublish', 'siteaccess');

        $lists = $campaigns = [];

        $mailingCounter = $subscriptionCounter = 0;

        // Mailing Lists

        $sql = 'SELECT id, name, lang FROM ezmailingmailinglist WHERE draft = 0';

        $list_rows = $this->runQuery($sql);
        foreach ($list_rows as $list_row) {
            $fileName = $this->ioService->saveFile(
                self::DUMP_FOLDER."/list/list_{$list_row['id']}.json",
                json_encode([$list_row['lang'] => $list_row['name']]) // Approve should be false when importing
            );
            $lists[]  = pathinfo($fileName)['filename'];
        }

        // Campaigns

        $sql = 'SELECT id, subject, sender_name, sender_email, report_email, destination_mailing_list ';

        $sql .= 'FROM ezmailingcampaign WHERE draft = 0';

        $campaign_rows = $this->runQuery($sql);
        foreach ($campaign_rows as $campaign_row) {
            $mailings = [];
            $fileName    = $this->ioService->saveFile(
                self::DUMP_FOLDER."/campaign/campaign_{$campaign_row['id']}.json",
                json_encode(
                    [
                        'name'       => $campaign_row['subject'],
                        'senderName'  => $campaign_row['sender_name'],
                        'senderEmail' => $campaign_row['sender_email'],
                        'reportEmail' => $campaign_row['report_email'],
                        'mailings'    => $mailings
                    ]
                )
            );
            $campaigns[] = pathinfo($fileName)['filename'];

        }

        // getting users
        $users = [];

        $this->ioService->saveFile(
            self::DUMP_FOLDER.'/manifest.json',
            json_encode(['lists' => $lists, 'campaigns' => $campaigns, 'users' => $users])
        );
        $this->io->section(
            'Total: '.count($lists).' lists, '.count($campaigns).' campaigns, '.$mailingCounter.' mailings, '.
            count($users).' users, '.$subscriptionCounter.' subscriptions.'
        );
        $this->io->success('Export done.');
    }

    private function import(): void
    {
        // clear the tables, reset the IDs
        $this->clean();
        $this->io->section('Importing from json files to new database.');

        $manifest  = $this->ioService->readFile(self::DUMP_FOLDER.'/manifest.json');
        $fileNames = json_decode($manifest);

        // Importing Lists
        $listCounter = $mailingCounter = $userCounter = $subscriptionCounter = 0;
        $listIds     = [];

        $mailingListRepository = $this->entityManager->getRepository(MailingList::class);
        $userRepository        = $this->entityManager->getRepository(User::class);

        foreach ($fileNames->lists as $listFile) {
            $listData    = json_decode($this->ioService->readFile(self::DUMP_FOLDER.'/list/'.$listFile.'.json'));
            $mailingList = new MailingList();
            $mailingList->setNames((array) $listData->names);
            $mailingList->setWithApproval((bool) $listData->withApproval);
            $this->entityManager->persist($mailingList);
            ++$listCounter;
            $this->entityManager->flush();
            $listIds[explode('_', $listFile)[1]] = $mailingList->getId();
        }

        // Importing campaigns with mailings
        foreach ($fileNames->campaigns as $campaignFile) {
            $campaignData = json_decode($this->ioService->readFile(self::DUMP_FOLDER.'/campaign/'.$campaignFile.'.json'));
            $campaign     = new Campaign();
            $campaign->setNames((array) $campaignData->names);
            $campaign->setReportEmail($campaignData->reportEmail);
            $campaign->setSenderEmail($campaignData->senderEmail);
            $campaign->setReturnPathEmail('');
            $campaign->setSenderName($campaignData->senderName);
            $campaign->setLocationId($campaignData->locationId);
            $campaignContentId = explode('_', $campaignFile)[1];
            if (\array_key_exists($campaignContentId, $listIds)) {
                /* @var MailingList $mailingList */
                $mailingList = $mailingListRepository->findOneBy(
                    ['id' => $listIds[$campaignContentId]]
                );
                $campaign->addMailingList($mailingList);
            }
            foreach ($campaignData->mailings as $mailingData) {
                $mailing = new Mailing();
                $mailing->setNames((array) $mailingData->names);
                $mailing->setStatus($mailingData->status);
                $mailing->setRecurring(false);
                $mailing->setHoursOfDay([$mailingData->hoursOfDay]);
                $mailing->setDaysOfMonth([$mailingData->daysOfMonth]);
                $mailing->setMonthsOfYear([$mailingData->monthsOfYear]);
                $mailing->setLocationId($mailingData->locationId);
                $mailing->setSiteAccess($mailingData->siteAccess);
                $mailing->setSubject($mailingData->subject);
                $this->entityManager->persist($mailing);
                $campaign->addMailing($mailing);
                ++$mailingCounter;
            }
            $this->entityManager->persist($campaign);
        }

        // Importing Users & Subscriptions
        foreach ($fileNames->users as $userFile) {
            $userData = json_decode($this->ioService->readFile(self::DUMP_FOLDER.'/user/'.$userFile.'.json'));

            // check if email already exists
            $existingUser = $userRepository->findOneBy(['email' => $userData->email]);
            if (null === $existingUser) {
                $user = new User();
                $user
                    ->setEmail($userData->email)
                    ->setBirthDate($userData->birthDate)
                    ->setCompany($userData->company)
                    ->setFirstName($userData->firstName)
                    ->setLastName($userData->lastName)
                    ->setStatus($userData->status)
                    ->setOrigin('site');

                foreach ($userData->subscriptions as $subscription) {
                    if (\array_key_exists($subscription->list_contentobject_id, $listIds)) {
                        $registration = new Registration();
                        /* @var MailingList $mailingList */
                        $mailingList = $mailingListRepository->findOneBy(
                            ['id' => $listIds[$subscription->list_contentobject_id]]
                        );
                        $registration->setMailingList($mailingList);
                        $registration->setApproved($subscription->approved);
                        $user->addRegistration($registration);
                        ++$subscriptionCounter;
                    }
                }
                $this->entityManager->persist($user);
                ++$userCounter;
            }
        }
        $this->entityManager->flush();

        $this->io->section(
            'Total: '.$listCounter.' lists, '.$mailingCounter.' mailings, '.$userCounter.' users, '.
            $subscriptionCounter.' registrations.'
        );
        $this->io->success('Import done.');
    }

    private function clean(): void
    {
        // We don't run TRUNCATE command here because of foreign keys constraints
        $this->entityManager->getConnection()->query('DELETE FROM novaezmailing_stats_hit');
        $this->entityManager->getConnection()->query('ALTER TABLE novaezmailing_stats_hit AUTO_INCREMENT = 1');
        $this->entityManager->getConnection()->query('DELETE FROM novaezmailing_broadcast');
        $this->entityManager->getConnection()->query('ALTER TABLE novaezmailing_broadcast AUTO_INCREMENT = 1');
        $this->entityManager->getConnection()->query('DELETE FROM novaezmailing_mailing');
        $this->entityManager->getConnection()->query('ALTER TABLE novaezmailing_mailing AUTO_INCREMENT = 1');
        $this->entityManager->getConnection()->query('DELETE FROM novaezmailing_campaign_mailinglists_destination');
        $this->entityManager->getConnection()->query('DELETE FROM novaezmailing_campaign');
        $this->entityManager->getConnection()->query('ALTER TABLE novaezmailing_campaign AUTO_INCREMENT = 1');
        $this->entityManager->getConnection()->query('DELETE FROM novaezmailing_confirmation_token');
        $this->entityManager->getConnection()->query('DELETE FROM novaezmailing_registrations');
        $this->entityManager->getConnection()->query('ALTER TABLE novaezmailing_registrations AUTO_INCREMENT = 1');
        $this->entityManager->getConnection()->query('DELETE FROM novaezmailing_mailing_list');
        $this->entityManager->getConnection()->query('ALTER TABLE novaezmailing_mailing_list AUTO_INCREMENT = 1');
        $this->entityManager->getConnection()->query('DELETE FROM novaezmailing_user');
        $this->entityManager->getConnection()->query('ALTER TABLE novaezmailing_user AUTO_INCREMENT = 1');
        $this->io->section('Current tables in the new database have been cleaned.');
    }

    private function runQuery(string $sql, array $parameters = [], $fetchMode = null): array
    {
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        for ($i = 1, $iMax = count($parameters); $i <= $iMax; ++$i) {
            $stmt->bindValue($i, $parameters[$i - 1]);
        }
        $stmt->execute();

        return $stmt->fetchAll($fetchMode);
    }
}
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

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Novactive\Bundle\eZMailingBundle\Core\IOService;
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

/**
 * @SuppressWarnings(PHPMD)
 */
class MigrateCjwnlCommand extends Command
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

    public const DEFAULT_FALLBACK_CONTENT_ID = 1;

    public const DUMP_FOLDER = 'migrate/cjwnl';

    public function __construct(
        IOService $ioService,
        EntityManagerInterface $entityManager,
        Repository $ezRepository,
        ConfigResolverInterface $configResolver
    ) {
        parent::__construct();
        $this->ioService = $ioService;
        $this->entityManager = $entityManager;
        $this->ezRepository = $ezRepository;
        $this->configResolver = $configResolver;
    }

    protected function configure(): void
    {
        $this
            ->setName('novaezmailing:migrate:cjwnl')
            ->setDescription('Import database from the old one.')
            ->addOption('export', null, InputOption::VALUE_NONE, 'Export from old DB to json files')
            ->addOption('import', null, InputOption::VALUE_NONE, 'Import from json files to new DB')
            ->addOption('clean', null, InputOption::VALUE_NONE, 'Clean the existing data')
            ->setHelp('Run novaezmailing:migrate:cjwnl --export|--import|--clean');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
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
            $this->io->error('No export or import option found. Run novaezmailing:migrate:cjwnl --export|--import');
        }

        return Command::SUCCESS;
    }

    private function export(): void
    {
        // clean the 'ezmailing' dir
        $this->ioService->cleanDir(self::DUMP_FOLDER);
        $this->io->section('Cleaned the folder with json files.');
        $this->io->section('Exporting from old database to json files.');

        $contentService = $this->ezRepository->getContentService();
        $contentLanguageService = $this->ezRepository->getContentLanguageService();
        $languages = $contentLanguageService->loadLanguages();
        $defaultLanguageCode = $contentLanguageService->getDefaultLanguageCode();
        $siteAccessList = $this->configResolver->getParameter('list', 'ezpublish', 'siteaccess');

        $lists = $campaigns = [];

        $mailingCounter = $registrationCounter = 0;

        // Lists, Campaigns with Mailings

        $this->io->writeln('Lists with Campaigns with Mailings:');

        $sql = 'SELECT contentobject_attribute_version, contentobject_id, auto_approve_registered_user,';

        $sql .= 'email_sender_name, email_sender, email_receiver_test FROM cjwnl_list ';
        $sql .= 'WHERE (contentobject_id ,contentobject_attribute_version) IN ';
        $sql .= '(SELECT contentobject_id, MAX(contentobject_attribute_version) ';
        $sql .= 'FROM cjwnl_list GROUP BY contentobject_id)';

        $list_rows = $this->runQuery($sql);

        $this->io->progressStart(count($list_rows));

        foreach ($list_rows as $list_row) {
            try {
                $listContent = $contentService->loadContent($list_row['contentobject_id']);
            } catch (\Exception $e) {
                try {
                    $listContent = $contentService->loadContent(self::DEFAULT_FALLBACK_CONTENT_ID);
                } catch (\Exception $e) {
                    continue;
                }
            }
            $listNames = [];
            foreach ($languages as $language) {
                $title = $listContent->getName($language->languageCode);
                if (null !== $title) {
                    $listNames[$language->languageCode] = $title;
                }
            }
            $fileName = $this->ioService->saveFile(
                self::DUMP_FOLDER."/list/list_{$list_row['contentobject_id']}.json",
                json_encode(
                    ['names' => $listNames, 'withApproval' => $list_row['auto_approve_registered_user']]
                )
            );
            $lists[] = pathinfo($fileName)['filename'];

            $mailings = [];

            $sql = 'SELECT edition_contentobject_id, status, siteaccess, mailqueue_process_finished ';

            $sql .= 'FROM cjwnl_edition_send WHERE list_contentobject_id = ?';

            $mailing_rows = $this->runQuery($sql, [$list_row['contentobject_id']]);
            foreach ($mailing_rows as $mailing_row) {
                switch ($mailing_row['status']) {
                    case 0:
                        $status = Mailing::PENDING;
                        break;
                    case 1:
                        $status = Mailing::PENDING;
                        break;
                    case 2:
                        $status = Mailing::PROCESSING;
                        break;
                    case 3:
                        $status = Mailing::SENT;
                        break;
                    case 9:
                        $status = Mailing::ABORTED;
                        break;
                    default:
                        $status = Mailing::DRAFT;
                        break;
                }

                try {
                    $mailingContent = $contentService->loadContent($mailing_row['edition_contentobject_id']);
                } catch (\Exception $e) {
                    $mailingContent = $contentService->loadContent(self::DEFAULT_FALLBACK_CONTENT_ID);
                }
                $mailingNames = [];
                foreach ($languages as $language) {
                    $title = $mailingContent->getName($language->languageCode);
                    if (null !== $title) {
                        $mailingNames[$language->languageCode] = $title;
                    }
                }
                $siteAccess = \in_array(
                    $mailing_row['siteaccess'],
                    $siteAccessList,
                    true
                ) ? $mailing_row['siteaccess'] : $siteAccessList[0];

                $mailings[] = [
                    'names' => $mailingNames,
                    'status' => $status,
                    'siteAccess' => $siteAccess,
                    'locationId' => $mailingContent->contentInfo->mainLocationId,
                    'hoursOfDay' => (int) date('H', (int) $mailing_row['mailqueue_process_finished']),
                    'daysOfMonth' => (int) date('d', (int) $mailing_row['mailqueue_process_finished']),
                    'monthsOfYear' => (int) date('m', (int) $mailing_row['mailqueue_process_finished']),
                    'subject' => $mailingContent->getName($defaultLanguageCode) ?? array_shift($mailingNames),
                ];
                ++$mailingCounter;
            }

            $fileName = $this->ioService->saveFile(
                self::DUMP_FOLDER."/campaign/campaign_{$list_row['contentobject_id']}.json",
                json_encode(
                    [
                        'names' => $listNames,
                        'locationId' => $listContent->contentInfo->mainLocationId,
                        'senderName' => $list_row['email_sender_name'],
                        'senderEmail' => $list_row['email_sender'],
                        'reportEmail' => $list_row['email_receiver_test'],
                        'mailings' => $mailings,
                    ]
                )
            );
            $campaigns[] = pathinfo($fileName)['filename'];
            $this->io->progressAdvance();
        }

        $this->io->progressFinish();

        // Users
        $this->io->writeln('Users with Subscriptions:');

        $users = [];

        $sql = 'SELECT max(id) as `id`, email, first_name, last_name, organisation, '.
               ' birthday, ez_user_id, status, confirmed, ';

        $sql .= 'bounced, blacklisted FROM cjwnl_user WHERE removed = 0 ';
        $sql .= 'GROUP BY email';

        $user_rows = $this->runQuery($sql);

        $this->io->progressStart(count($user_rows));

        foreach ($user_rows as $user_row) {
            $status = User::PENDING;
            if ($user_row['confirmed']) {
                $status = User::CONFIRMED;
            }
            if ($user_row['bounced']) {
                $status = User::SOFT_BOUNCE;
            }
            if ($user_row['blacklisted']) {
                $status = User::BLACKLISTED;
            }
            $birthdate = empty($user_row['birthday']) ? null : new DateTime('2018-12-11');

            // Registrations
            $sql = 'SELECT list_contentobject_id, approved FROM'.
                   ' cjwnl_subscription WHERE newsletter_user_id = ?';
            $subscription_rows = $this->runQuery($sql, [$user_row['id']]);
            $subscriptions = [];
            foreach ($subscription_rows as $subscription_row) {
                $subscriptions[] = [
                    'list_contentobject_id' => $subscription_row['list_contentobject_id'],
                    'approved' => (bool) $subscription_row['approved'],
                ];
                ++$registrationCounter;
            }

            $fileName = $this->ioService->saveFile(
                self::DUMP_FOLDER."/user/user_{$user_row['id']}.json",
                json_encode(
                    [
                        'email' => $user_row['email'],
                        'firstName' => $user_row['first_name'],
                        'lastName' => $user_row['last_name'],
                        'birthDate' => $birthdate,
                        'status' => $status,
                        'company' => $user_row['organisation'],
                        'subscriptions' => $subscriptions,
                    ]
                )
            );
            $users[] = pathinfo($fileName)['filename'];

            $this->io->progressAdvance();
        }

        $this->ioService->saveFile(
            self::DUMP_FOLDER.'/manifest.json',
            json_encode(['lists' => $lists, 'campaigns' => $campaigns, 'users' => $users])
        );

        $this->io->progressFinish();

        $this->io->section(
            'Total: '.count($lists).' lists, '.count($campaigns).' campaigns, '.$mailingCounter.' mailings, '.
            count($users).' users, '.$registrationCounter.' registrations.'
        );
        $this->io->success('Export done.');
    }

    private function import(): void
    {
        // clear the tables, reset the IDs
        $this->clean();
        $this->io->section('Importing from json files to new database.');

        $manifest = $this->ioService->readFile(self::DUMP_FOLDER.'/manifest.json');
        $fileNames = json_decode($manifest);

        // Lists
        $this->io->writeln('Lists:');
        $this->io->progressStart(count($fileNames->lists));

        $listCounter = $campaignCounter = $mailingCounter = $userCounter = $registrationCounter = 0;
        $listIds = [];

        $mailingListRepository = $this->entityManager->getRepository(MailingList::class);
        $userRepository = $this->entityManager->getRepository(User::class);

        $n = 0;
        foreach ($fileNames->lists as $listFile) {
            $listData = json_decode($this->ioService->readFile(self::DUMP_FOLDER.'/list/'.$listFile.'.json'));
            $mailingList = new MailingList();
            $mailingList->setNames((array) $listData->names);
            $mailingList->setWithApproval((bool) $listData->withApproval);
            $this->entityManager->persist($mailingList);
            ++$listCounter;
            $this->entityManager->flush();
            $listIds[explode('_', $listFile)[1]] = $mailingList->getId();
            ++$n;
            if (0 === $n % 100) {
                $this->entityManager->clear();
            }
            $this->io->progressAdvance();
        }
        $this->io->progressFinish();

        // Campaigns with Mailings
        $this->io->writeln('Campaigns with Mailings:');
        $this->io->progressStart(count($fileNames->campaigns));

        $n = 0;
        foreach ($fileNames->campaigns as $campaignFile) {
            $campaignData = json_decode(
                $this->ioService->readFile(self::DUMP_FOLDER.'/campaign/'.$campaignFile.'.json')
            );
            $campaign = new Campaign();
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
                if (null !== $mailingList) {
                    $campaign->addMailingList($mailingList);
                }
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
            ++$campaignCounter;
            ++$n;
            if (0 === $n % 100) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
            $this->io->progressAdvance();
        }
        $this->entityManager->flush();
        $this->io->progressFinish();

        // Users & Registrations
        $this->io->writeln('Users and Registrations:');
        $this->io->progressStart(count($fileNames->users));

        $n = 0;
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
                        if (null !== $mailingList) {
                            $registration->setMailingList($mailingList);
                        }
                        $registration->setApproved($subscription->approved);
                        $user->addRegistration($registration);
                        ++$registrationCounter;
                    }
                }
                $this->entityManager->persist($user);
                ++$userCounter;
                ++$n;
                if (0 === $n % 100) {
                    $this->entityManager->flush();
                    $this->entityManager->clear();
                }
            }
            $this->io->progressAdvance();
        }
        $this->entityManager->flush();

        $this->io->progressFinish();

        $this->io->section(
            'Total: '.$listCounter.' lists, '.$campaignCounter.' campaigns, '.$mailingCounter.' mailings, '.
            $userCounter.' users, '.$registrationCounter.' registrations.'
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

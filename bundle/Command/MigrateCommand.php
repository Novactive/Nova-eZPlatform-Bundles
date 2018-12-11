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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Novactive\Bundle\eZMailingBundle\Core\IOService;
use eZ\Publish\Core\Repository\Repository;

/**
 * Class MigrateCommand.
 */
class MigrateCommand extends Command
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

    public const CAMPAIGN_LIST_CONTENT_ID = 53;

    /**
     * MigrateCommand constructor.
     */
    public function __construct(IOService $ioService, EntityManagerInterface $entityManager, Repository $ezRepository)
    {
        parent::__construct();
        $this->ioService     = $ioService;
        $this->entityManager = $entityManager;
        $this->ezRepository  = $ezRepository;
    }

    protected function configure(): void
    {
        $this
            ->setName('novaezmailing:migrate')
            ->setDescription('Import database from the old one.')
            ->addOption('export', null, InputOption::VALUE_NONE, 'Export from old DB to json files')
            ->addOption('import', null, InputOption::VALUE_NONE, 'Import from json files to new DB')
            ->addOption('clean', null, InputOption::VALUE_NONE, 'Clean the existing data')
            ->setHelp('Run novaezmailing:migrate --export|--import|--clean');
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
            $this->io->error('No export or import option found. Run novaezmailing:migrate --export|--import');
        }
    }

    private function export(): void
    {
        // Get the Lists first, then Users and subscriptions (which are supposed to be registrations)

        $contentService         = $this->ezRepository->getContentService();
        $contentLanguageService = $this->ezRepository->getContentLanguageService();
        $languages              = $contentLanguageService->loadLanguages();

        $lists = $campaigns = [];

        $sql = 'SELECT contentobject_attribute_version, contentobject_id, auto_approve_registered_user,';

        $sql .= 'email_sender_name, email_sender, email_receiver_test from cjwnl_list ';
        $sql .= 'WHERE (contentobject_id ,contentobject_attribute_version) IN ';
        $sql .= '(SELECT contentobject_id, MAX(contentobject_attribute_version) ';
        $sql .= 'FROM cjwnl_list GROUP BY contentobject_id)';

        $list_rows = $this->runQuery($sql);
        foreach ($list_rows as $row) {
            try {
                $content = $contentService->loadContent($row['contentobject_id']);
            } catch (\Exception $e) {
                $content = $contentService->loadContent(self::CAMPAIGN_LIST_CONTENT_ID);
            }
            $names = [];
            foreach ($languages as $language) {
                $names[$language->languageCode] = $content->getFieldValue('title', $language->languageCode)->text;
            }
            $lists[] = basename(
                $this->ioService->saveFile(
                    "ezmailing/list/list_{$row['contentobject_id']}.json",
                    json_encode(['names' => $names, 'approbation' => $row['auto_approve_registered_user']])
                )
            );
        }

        $this->ioService->saveFile('ezmailing/manifest.json', json_encode(['lists' => $lists]));
        $this->io->section(
            'Total: '.(string) count($lists).' lists.'
        );

        $this->io->success('Export done.');
    }

    private function import(): void
    {
        // clear the tables, reset the IDs
        $this->clean();

        $manifest     = $this->ioService->readFile('ezmailing/manifest.json');
        $fileNames    = json_decode($manifest);
        $listsCounter = 0;

        $this->io->section(
            'Total: '.$listsCounter.' lists, '
        );

        $this->io->success('Import done.');
    }

    private function clean(): void
    {
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
        $this->io->success('Current tables cleaned.');
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

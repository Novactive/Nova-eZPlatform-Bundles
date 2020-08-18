<?php

/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <j.canat@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Core\Import;

use Carbon\Carbon;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Generator;
use Novactive\Bundle\eZMailingBundle\Core\DataHandler\UserImport;
use Novactive\Bundle\eZMailingBundle\Entity\MailingList;
use Novactive\Bundle\eZMailingBundle\Entity\Registration;
use Novactive\Bundle\eZMailingBundle\Entity\User as UserEntity;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Class Importer.
 */
class User
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Importer constructor.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function rowsIterator(UserImport $userImport): Generator
    {
        $spreadsheet = IOFactory::load($userImport->getFile()->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        foreach ($worksheet->getRowIterator() as $row) {
            if (1 === $row->getRowIndex()) {
                continue;
            }
            $cells = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            yield $cells;
        }
    }

    /**
     * Hydrate user.
     *
     * @return User
     */
    public function hydrateUser(array $cells): UserEntity
    {
        $repo = $this->entityManager->getRepository(UserEntity::class);
        $user = new UserEntity();
        if (isset($cells[0])) {
            $user = $repo->findOneByEmail($cells[0]);
            if (!$user instanceof UserEntity) {
                $user = new UserEntity();
                $user->setEmail(filter_var($cells[0], FILTER_SANITIZE_EMAIL));
            }
        }
        if (isset($cells[1])) {
            $user->setFirstName($cells[1]);
        }
        if (isset($cells[2])) {
            $user->setLastName($cells[2]);
        }
        if (isset($cells[3])) {
            $user->setGender($cells[3]);
        }
        if (isset($cells[4])) {
            try {
                $date = Carbon::createFromFormat('Y-m-d', (string) $cells[4]);
            } catch (Exception $e) {
                $date = Date::excelToDateTimeObject((string) $cells[4]);
            }
            $user->setBirthDate($date);
        }
        if (isset($cells[5])) {
            $user->setPhone((string) $cells[5]);
        }
        if (isset($cells[6])) {
            $user->setZipcode((string) $cells[6]);
        }
        if (isset($cells[7])) {
            $user->setCity($cells[7]);
        }
        if (isset($cells[8])) {
            $user->setState($cells[8]);
        }
        if (isset($cells[9])) {
            $user->setCountry($cells[9]);
        }
        if (isset($cells[10])) {
            $user->setJobTitle($cells[10]);
        }
        if (isset($cells[11])) {
            $user->setCompany($cells[11]);
        }
        $user->setRestricted(false);
        $user->setOrigin('import');
        $user->setStatus(UserEntity::CONFIRMED);

        return $user;
    }

    /**
     * Register the user to the MailingList.
     *
     * @return User
     */
    public function registerUser(UserEntity $user, MailingList $mailingList): UserEntity
    {
        $registration = new Registration();
        $registration
            ->setUser($user)
            ->setMailingList($mailingList)
            ->setApproved(true)
            ->setUpdated(new DateTime());
        $user->addRegistration($registration);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}

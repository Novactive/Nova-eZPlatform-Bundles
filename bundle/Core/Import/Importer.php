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

use Doctrine\ORM\EntityManager;
use Novactive\Bundle\eZMailingBundle\Entity\MailingList;
use Novactive\Bundle\eZMailingBundle\Entity\Registration;
use Novactive\Bundle\eZMailingBundle\Entity\User;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class User
 * @package Novactive\Bundle\eZMailingBundle\Core\Import
 */
class Importer
{
    /** @var EntityManager */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param UploadedFile $file
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function getRawData(UploadedFile $file) : array
    {
        $spreadsheet = IOFactory::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = [];
        $headers = [];
        foreach ($worksheet->getRowIterator() as $row) {
            $cells = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            if (count($headers) == 0) {
                $headers = array_map('trim', $cells);
            } else {
                $rows[] = array_combine($headers, array_map('trim', $cells));
            }
        }

        return $rows;
    }

    /**
     * Hydrate user
     * @param array $row
     * @return User
     */
    public function getUser(array $row): User
    {
        $repo = $this->entityManager->getRepository('NovaeZMailingBundle:User');
        $user = $repo->findOneBy([
            'email' => $row['email'] ?? ''
        ]);
        if(!$user instanceof  User) {
            $user = new User();
        }
        $user->setEmail(filter_var($row['email'] ?? '', FILTER_SANITIZE_EMAIL));
        $user->setFirstName($row['firstName'] ?? '');
        $user->setLastName($row['lastName'] ?? '');
        $user->setGender($row['gender'] ?? '');
        $date = $row['birthDate'] ?? '';
        if( '' !== $date ) {
            try {
                $dob = Date::excelToDateTimeObject($date);
                $dob->setTime(0, 0, 0);
                $user->setBirthDate($dob);
            }catch(\Exception $e) {
            }
        }
        $user->setPhone($row['phone'] ?? '');
        $user->setCity($row['city'] ?? '');
        $user->setState($row['state'] ?? '');
        $user->setOrigin($row['origin'] ?? 'import');
        $user->setStatus($row['status'] ?? '');
        $user->setRestricted((bool)$row['restricted']);

        return $user;
    }

    /**
     * Create a new user by mailingList
     * @param User $user
     * @param MailingList $mailingList
     * @return User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createUser(User $user, MailingList $mailingList) : User
    {
        $registration = new Registration();
        $registration->setUser($user);
        $registration->setMailingList($mailingList);
        $user->setRegistrations([$registration]);
        $this->entityManager->persist($user);
        $this->entityManager->flush($user);

        return $user;
    }
}
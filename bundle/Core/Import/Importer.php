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
use Novactive\Bundle\eZMailingBundle\Entity\User;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
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
     * Check if the file extension is valid
     * @param FormInterface $form
     * @param UploadedFile $file
     * @return FormInterface
     */
    public function checkFileExtension(FormInterface $form, UploadedFile $file) : FormInterface
    {
        $extension = $file->getClientOriginalExtension();
        if(!$file instanceof UploadedFile || !in_array($extension, ['xls', 'xlsx']) ) {
            $form->addError(new FormError('The file is invalid'));
        }

        return $form;
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
                $headers = $cells;
            } else {
                $rows[] = array_combine($headers, $cells);
            }
        }

        return $rows;
    }

    /**
     * @param FormInterface $form
     * @param array $data
     * @return bool
     */
    public function checkUserData(FormInterface $form, array $data) : bool
    {
        $statuses = User::STATUSES;
        $email = $data['email'] ?? '';
        $isError = false;
        if( strlen($email) == 0 ) {
            $isError = true;
            $form->addError(new FormError('The email is empty '));
        } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $isError = true;
            $form->addError(new FormError('The email "'.$email.'" is invalid'));
        } else {
            $repo = $this->entityManager->getRepository(User::class);
            if ($repo->isAlreadyExist($email)) {
                $isError = true;
                $form->addError(new FormError('This email ' . $email . ' is already used.'));
            }
        }
        $origin = $data['origin'] ?? '';
        if( empty($origin) ) {
            $isError = true;
            $form->addError( new FormError('For the user of '. $email . ', the origin field is mandatory'));
        }
        $status = $data['status'] ?? '';
        if( empty($status) ) {
            $isError = true;
            $form->addError( new FormError('For the user of  '. $email . ', the status field is mandatory'));
        } elseif( !in_array($status, $statuses) ) {
            $isError = true;
            $form->addError(new FormError('For the user of '. $email. ', the status "' .$status. '" is invalid') );
        }
        $restricted = $data['restricted'] ?? null;
        if( is_null($restricted) ) {
            $isError = true;
            $form->addError( new FormError('For the user of '. $email . ', the restricted field is mandatory'));
        }
        $date = $data['birthDate'] ?? '';
        if( !empty($date) && !$this->isValidDate($date) ) {
            $isError = true;
            $form->addError( new FormError('For the user of '.$email. ', the date format "'.$date.'" is invalid') );
        }

        return $isError;
    }

    /**
     * Create a new user for a given row
     * @param array $row
     * @return User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createUser(array $row) : User
    {
        $user = new User();
        $user->setEmail(filter_var($row['email'], FILTER_SANITIZE_EMAIL) ?? '');
        $user->setFirstName($row['firstName'] ?? '');
        $user->setLastName($row['lastName'] ?? '');
        $user->setGender($row['gender'] ?? '');
        $date = $row['birthDate'] ?? '';
        if( '' !== $date ) {
            $dob = new \DateTime($date);
            $dob->setTime(0, 0, 0);
            $user->setBirthDate($dob);
        }
        $user->setPhone($row['phone'] ?? '');
        $user->setCity($row['city'] ?? '');
        $user->setState($row['state'] ?? '');
        $user->setOrigin($row['origin'] ?? 'import');
        $user->setStatus($row['status'] ?? '');
        $user->setRestricted((bool)$row['restricted'] ?? false);
        $user->setConfirmationToken($row['confirmationToken'] ?? '');

        $this->entityManager->persist($user);
        $this->entityManager->flush($user);

        return $user;
    }

    /**
     * Check if the date is valid
     * @param $date
     * @return bool
     */
    public function isValidDate($date) : bool
    {
        $dateTime = null;
        if( is_string($date) ) {
            try {
                $dateTime = new \DateTime($date);
            } catch (\Exception $e) {
            }
        }
        return $dateTime instanceof \DateTime;
    }
}
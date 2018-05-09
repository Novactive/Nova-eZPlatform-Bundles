<?php
declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Controller\Admin;

use Doctrine\ORM\EntityManager;
use eZ\Publish\Core\Helper\TranslationHelper;
use Novactive\Bundle\eZMailingBundle\Entity\User;
use Novactive\Bundle\eZMailingBundle\Form\ImportType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class UserController.
 *
 * @Route("/import")
 */
class ImportController
{
    /**
     * @Route("/user", name="novaezmailing_import_user")
     * @Template("@NovaeZMailing/admin/import/user.html.twig")
     * @param Request $request
     * @param RouterInterface $router
     * @param EntityManager $entityManager
     * @param FormFactoryInterface $formFactory
     * @param TranslationHelper $translationHelper
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function userAction(
        Request $request,
        RouterInterface $router,
        EntityManager $entityManager,
        FormFactoryInterface $formFactory,
        TranslationHelper $translationHelper
    )
    {
        $form = $formFactory->create(ImportType::class, null);
        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid() ) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = [];
            $headers  = [];
            foreach($worksheet->getRowIterator() as $row) {
                $cells = [];
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                foreach($cellIterator as $cell) {
                    $cells[] = $cell->getValue();
                }
                if(count($headers) == 0) {
                    $headers = $cells;
                } else {
                    $rows[] = array_combine($headers, $cells);
                }
            }
            foreach($rows as $row) {
                /** @var Form $form */
                $errors = $this->checkUserData($entityManager, $form, $row);

                if( !$errors  ) {
                    $user = new User();
                    $email = isset($row['email']) ? filter_var($row['email'], FILTER_SANITIZE_EMAIL) : '' ;
                    $user->setEmail($email);
                    $user->setFirstName(isset($row['firstName']) ? $row['firstName'] : '');
                    $user->setLastName(isset($row['lastName']) ? $row['lastName'] : '');
                    $user->setGender(isset($row['gender']) ? $row['gender'] : '');
                    $dob = new \DateTime(isset($row['birthDate']) ? $row['birthDate'] : '' );
                    $user->setBirthDate($dob);
                    $user->setPhone(isset($row['phone']) ? $row['phone'] : '');
                    $user->setCity(isset($row['city']) ? $row['city'] : '');
                    $user->setState(isset($row['state']) ? $row['state'] : '');
                    $user->setOrigin(isset($row['origin']) ? $row['origin'] : '');
                    $user->setStatus(isset($row['status']) ? $row['status'] : '');
                    $user->setRestricted(isset($row['restricted']) ? (bool)$row['restricted'] : false);
                    $user->setConfirmationToken(isset($row['confirmationToken']) ? $row['confirmationToken'] : '');

                    $entityManager->persist($user);
                    $entityManager->flush();
                }
            }
        }
        return [
            'item' => null,
            'form' => $form->createView()
        ];
    }

    /**
     * @param EntityManager $entityManager
     * @param Form $form
     * @param array $data
     * @return bool
     */
    public function checkUserData(EntityManager $entityManager, Form $form, array $data) : bool
    {
        $errors = [];
        $statuses = User::STATUSES;
        $email = isset($data['email']) ? $data['email'] : '';
        $isError = false;
        if(strlen($email) == 0 ) {
            $isError = true;
            $errors[] = 'The email is empty ';
            $form->addError(new FormError('The email is empty '));
        } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $isError = true;
            $errors[] = 'The email "'.$email.'" is invalid';
            $form->addError(new FormError('The email "'.$email.'" is invalid'));
        } else {
            $repo = $entityManager->getRepository(User::class);
            if ($repo->isAlreadyExist($email)) {
                $isError = true;
                $errors[] = 'This email ' . $email . ' is already used.';
                $form->addError(new FormError('This email ' . $email . ' is already used.'));
            }
        }
        $origin = isset($data['origin']) ? $data['origin'] : '';
        if( empty($origin) ) {
            $isError = true;
            $errors[] = 'For the user of '. $email . ', the origin field is mandatory';
            $form->addError( new FormError('For the user of '. $email . ', the origin field is mandatory'));
        }
        $status = isset($data['status']) ? $data['status'] : '';
        if( empty($status) ) {
            $isError = true;
            $errors[] = 'For the user of  '. $email . ', the status field is mandatory';
            $form->addError( new FormError('For the user of  '. $email . ', the status field is mandatory'));
        } elseif( !in_array($status, $statuses) ) {
            $isError = true;
            $errors[] = 'For the user of '. $email. ', the status ' .$status. ' is invalid';
            $form->addError(new FormError('For the user of '. $email. ', the status "' .$status. '" is invalid') );
        }
        $restricted = isset($data['restricted']) ? (bool)$data['restricted'] : null;
        if( is_null($restricted) ) {
            $isError = true;
            $errors[] = 'For the user of '. $email . ', the restricted field is mandatory';
            $form->addError( new FormError('For the user of '. $email . ', the restricted field is mandatory'));
        }

        return $isError;
    }
}

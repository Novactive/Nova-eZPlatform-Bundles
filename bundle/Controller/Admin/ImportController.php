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

namespace Novactive\Bundle\eZMailingBundle\Controller\Admin;

use Novactive\Bundle\eZMailingBundle\Core\Import\Importer;
use Novactive\Bundle\eZMailingBundle\Form\ImportType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

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
     * @param FormFactoryInterface $formFactory
     * @param Importer $importer
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function userAction(
        Request $request,
        FormFactoryInterface $formFactory,
        Importer $importer
    )
    {
        $form = $formFactory->create(ImportType::class, null);
        $form->handleRequest($request);
        $count = 0;

        if( $form->isSubmitted() ) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            $form = $importer->checkFileExtension($form, $file);

            if( $form->isValid() ) {
                $rows = $importer->getRawData($file);
                foreach ($rows as $row) {
                    $errors = $importer->checkUserData($form, $row);
                    if ( !$errors ) {
                        $user = $importer->createUser($row);
                        if( $user->getId() > 0 ) {
                            ++$count;
                        }
                    }
                }
            }
        }

        return [
            'count' => $count,
            'form' => $form->createView()
        ];
    }
}

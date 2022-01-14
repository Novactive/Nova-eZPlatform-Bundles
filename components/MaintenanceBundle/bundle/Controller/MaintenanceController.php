<?php

/**
 * NovaeZMaintenanceBundle.
 *
 * @package   Novactive\NovaeZMaintenanceBundle
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZMaintenanceBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\NovaeZMaintenanceBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use Novactive\NovaeZMaintenanceBundle\Form\Type\FilterType;
use Novactive\NovaeZMaintenanceBundle\Helper\FileHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/novamaintenance")
 */
class MaintenanceController extends Controller
{
    /**
     * @var FlashBagInterface
     */
    private FlashBagInterface $flashBag;

    /**
     * @var FileHelper
     */
    private FileHelper $fileHelper;

    public function __construct(FlashBagInterface $flashBag, FileHelper $fileHelper)
    {
        $this->flashBag = $flashBag;
        $this->fileHelper = $fileHelper;
    }

    /**
     * @Route("/", name="novamaintenance_index")
     */
    public function indexAction(): Response
    {
        $this->fileHelper->checkManageAccess();

        return $this->render('@ezdesign/maintenance/index.html.twig', [
            'maintenance_siteaccesses' => $this->fileHelper->getAvailableSiteaccessList(),
        ]);
    }

    /**
     * @Route("/manage/{maintenanceSiteaccess}", name="novamaintenance_manage")
     */
    public function manageAction(string $maintenanceSiteaccess, Request $request): Response
    {
        $this->fileHelper->checkManageAccess();
        $isExistFile = $this->fileHelper->isMaintenanceModeRunning($maintenanceSiteaccess);
        $btnLabel = $this->fileHelper->translate(!$isExistFile ? 'maintenance.start' : 'maintenance.stop');
        $form = $this->createForm(FilterType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->fileHelper->manageMaintenance($maintenanceSiteaccess);
            $this->flashBag->add(
                'success',
                $this->fileHelper->translate($isExistFile ? 'maintenance.stopped' : 'maintenance.started')
            );
            $btnLabel = $this->fileHelper->translate($isExistFile ? 'maintenance.start' : 'maintenance.stop');
        }

        return $this->render('@ezdesign/maintenance/manage.html.twig', [
            'form' => $form->createView(),
            'maintenance_siteaccess' => $maintenanceSiteaccess,
            'btnLabel' => $btnLabel,
        ]);
    }
}

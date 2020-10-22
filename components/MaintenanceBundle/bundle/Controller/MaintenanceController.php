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
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Novactive\NovaeZMaintenanceBundle\Form\Type\FilterType;
use Novactive\NovaeZMaintenanceBundle\Helper\FileHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
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
    private $flashBag;

    /**
     * @var FileHelper
     */
    private $fileHelper;

    public function __construct(FlashBagInterface $flashBag, FileHelper $fileHelper)
    {
        $this->flashBag = $flashBag;
        $this->fileHelper = $fileHelper;
    }

    /**
     * @Route("/manage", name="novamaintenance_manage")
     * @Template("@ezdesign/maintenance/manage.html.twig")
     */
    public function manageMaintenanceAction(Request $request, ConfigResolverInterface $configResolver): array
    {
        $filePath = $configResolver->getParameter('lock_file_id', 'nova_ezmaintenance');
        $isExistFile = $this->fileHelper->existFileCluster($filePath);

        $btnLabel = !$isExistFile ? 'Enable maintenance' : 'Disable maintenance';
        $form = $this->createForm(FilterType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $isExistFile ? $this->fileHelper->maintenanceUnLock($filePath) : $this->fileHelper->maintenanceLock(
                $filePath
            );
            $message = $isExistFile ? 'Disabled' : 'Enabled';
            $this->flashBag->add('success', 'Maintenance '.$message);
            $btnLabel = $isExistFile ? 'Enable maintenance' : 'Disable maintenance';
        }

        return [
            'form' => $form->createView(),
            'btnLabel' => $btnLabel,
        ];
    }
}

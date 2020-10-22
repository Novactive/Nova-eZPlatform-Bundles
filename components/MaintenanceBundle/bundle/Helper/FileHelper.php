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

namespace Novactive\NovaeZMaintenanceBundle\Helper;

use Exception;
use eZ\Publish\Core\IO\IOServiceInterface;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class FileHelper
{
    /**
     * @var IOServiceInterface
     */
    protected $binaryfileIOService;

    /**
     * @var Filesystem|null
     */
    private $fileSystem;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    public function __construct(
        IOServiceInterface $binaryFileIOService,
        Filesystem $fileSystem,
        Environment $twig,
        ConfigResolverInterface $configResolver
    ) {
        $this->binaryfileIOService = $binaryFileIOService;
        $this->fileSystem = $fileSystem;
        $this->twig = $twig;
        $this->configResolver = $configResolver;
    }

    public function createFileCluster(string $filePath): string
    {
        $localeFile = rtrim(sys_get_temp_dir(), '/').'/'.ltrim(basename($filePath), '/');
        $this->fileSystem->touch([$localeFile]);
        $binaryFile = $this->binaryfileIOService->newBinaryCreateStructFromLocalFile($localeFile);
        $binaryFile->id = $this->getFileId($filePath);

        $uri = $this->binaryfileIOService->createBinaryFile($binaryFile)->uri;

        $this->fileSystem->remove([$localeFile]);

        return $uri;
    }

    public function existFileCluster(string $filePath): bool
    {
        return $this->binaryfileIOService->exists($this->getFileId($filePath));
    }

    public function deleteFileCluster(string $filePath): bool
    {
        $binaryFileId = $this->getFileId($filePath);
        if ($this->binaryfileIOService->exists($binaryFileId)) {
            $this->binaryfileIOService->deleteBinaryFile($this->binaryfileIOService->loadBinaryFile($binaryFileId));
        }

        return true;
    }

    public function maintenanceUnLock(string $filePath): bool
    {
        if ($this->existFileCluster($filePath)) {
            $this->deleteFileCluster($filePath);

            return true;
        }

        return false;
    }

    public function maintenanceLock(string $filePath): bool
    {
        if ($this->existFileCluster($filePath)) {
            return false;
        }
        $this->createFileCluster($filePath);

        return true;
    }

    public function getResponse(?int $status = null): Response
    {
        try {
            $content = $this->twig->render(
                $this->configResolver->getParameter('template', 'nova_ezmaintenance')
            );
        } catch (Exception $exception) {
            $content = 'Une erreur inconnue s\'est produite';
        }
        $response = new Response();
        if (null !== $status) {
            $response->setStatusCode($status);
        }

        return $response->setContent($content);
    }

    private function getFileId(string $filePath): string
    {
        return 'nova_ezmaintenance/'.basename($filePath);
    }
}

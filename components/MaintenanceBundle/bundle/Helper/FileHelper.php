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
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\Core\Base\Exceptions\UnauthorizedException;
use eZ\Publish\Core\IO\IOServiceInterface;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class FileHelper
{
    public const CONFIG_NAMESPACE = 'nova_ezmaintenance';

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

    /**
     * @var array
     */
    private $siteaccessList;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var PermissionResolver
     */
    private $permissionResolver;

    public function __construct(
        IOServiceInterface $binaryFileIOService,
        Filesystem $fileSystem,
        Environment $twig,
        ConfigResolverInterface $configResolver,
        TranslatorInterface $translator,
        PermissionResolver $permissionResolver,
        array $siteaccessList = []
    ) {
        $this->binaryfileIOService = $binaryFileIOService;
        $this->fileSystem = $fileSystem;
        $this->twig = $twig;
        $this->configResolver = $configResolver;
        $this->translator = $translator;
        $this->permissionResolver = $permissionResolver;
        $this->siteaccessList = $siteaccessList;
    }

    public function existFileCluster(string $siteaccess): bool
    {
        return $this->binaryfileIOService->exists($this->getFileId($siteaccess));
    }

    public function isMaintenanceModeRunning(string $siteaccess): bool
    {
        $this->assertMaintenanceEnabled($siteaccess);

        return $this->existFileCluster($siteaccess);
    }

    public function manageMaintenance(string $siteaccess): void
    {
        $this->assertMaintenanceEnabled($siteaccess);
        $isExistFile = $this->isMaintenanceModeRunning($siteaccess);
        $isExistFile ? $this->maintenanceUnLock($siteaccess) : $this->maintenanceLock($siteaccess);
    }

    public function maintenanceUnLock(string $siteaccess): bool
    {
        if ($this->isMaintenanceModeRunning($siteaccess)) {
            $this->deleteFileCluster($siteaccess);

            return true;
        }

        return false;
    }

    public function maintenanceLock(string $siteaccess): bool
    {
        if ($this->existFileCluster($siteaccess)) {
            return false;
        }
        $this->createFileCluster($siteaccess);

        return true;
    }

    public function getResponse(string $siteaccess, ?int $status = null): Response
    {
        try {
            $content = $this->twig->render($this->getParameter('template', $siteaccess));
        } catch (Exception $exception) {
            $content = $this->translate('maintenance.response.unexpected_error');
        }
        $response = new Response();
        $status = $status ?? Response::HTTP_SERVICE_UNAVAILABLE;
        if (null !== $status) {
            $response->setStatusCode($status);
        }
        $response->headers->add(["X-Maintenance"=> 1]);

        return $response->setContent($content);
    }

    public function translate(string $id, $parameters = []): string
    {
        return $this->translator->trans($id, $parameters, self::CONFIG_NAMESPACE);
    }

    public function getAvailableSiteaccessList(): array
    {
        $siteaccessList = [];
        foreach ($this->siteaccessList as $item) {
            if (true === $this->isMaintenanceEnabled($item)) {
                $siteaccessList[$item] = $item;
            }
        }

        return $siteaccessList;
    }

    public function isMaintenanceEnabled(string $siteaccess): bool
    {
        if (true !== $this->hasParameter('enable', $siteaccess)) {
            return false;
        }

        return true === $this->getParameter('enable', $siteaccess);
    }

    public function assertMaintenanceEnabled(string $siteaccess): void
    {
        if (true !== $this->isMaintenanceEnabled($siteaccess)) {
            throw new RuntimeException(
                $this->translate('maintenance.disabled', ['%siteaccess%' => $siteaccess])
            );
        }
    }

    private function createFileCluster(string $siteaccess): string
    {
        $filePath = $this->getLockFile($siteaccess);
        $localeFile = rtrim(sys_get_temp_dir(), '/').'/'.ltrim(basename($filePath), '/');
        $this->fileSystem->touch([$localeFile]);
        $binaryFile = $this->binaryfileIOService->newBinaryCreateStructFromLocalFile($localeFile);
        $binaryFile->id = $this->getFileId($siteaccess);

        $uri = $this->binaryfileIOService->createBinaryFile($binaryFile)->uri;

        $this->fileSystem->remove([$localeFile]);

        return $uri;
    }

    /**
     * @return mixed|null
     */
    private function getParameter(string $paramName, string $siteaccess)
    {
        if ($this->hasParameter($paramName, $siteaccess)) {
            return $this->configResolver->getParameter($paramName, self::CONFIG_NAMESPACE, $siteaccess);
        }

        return null;
    }

    private function hasParameter(string $paramName, string $siteaccess): bool
    {
        return true === $this->configResolver->hasParameter($paramName, self::CONFIG_NAMESPACE, $siteaccess);
    }

    private function getLockFile(string $siteaccess): string
    {
        $this->assertMaintenanceEnabled($siteaccess);

        return (string) $this->getParameter('lock_file_id', $siteaccess);
    }

    private function getFileId(string $siteaccess): string
    {
        $lockFile = $this->getLockFile($siteaccess);

        return self::CONFIG_NAMESPACE.'/'.$siteaccess.'/'.basename($lockFile);
    }

    private function deleteFileCluster(string $siteaccess): bool
    {
        $binaryFileId = $this->getFileId($siteaccess);
        if ($this->binaryfileIOService->exists($binaryFileId)) {
            $this->binaryfileIOService->deleteBinaryFile($this->binaryfileIOService->loadBinaryFile($binaryFileId));
        }

        return true;
    }

    public function isClientIpAuthorized(string $client_ip, string $siteaccess): bool
    {
        $authorized_ips = (array) $this->getParameter('authorized_ips', $siteaccess);
        if (IPUtils::checkIp($client_ip, $authorized_ips)) {
            return true;
        }
        return false;
    }

    public function checkManageAccess(): void
    {
        if (!$this->permissionResolver->hasAccess('novamaintenance', 'manage')) {
            throw new UnauthorizedException('novamaintenance', 'manage', []);
        }
    }
}

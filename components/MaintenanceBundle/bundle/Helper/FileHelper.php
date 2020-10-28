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
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\IO\IOServiceInterface;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class FileHelper implements SiteAccess\SiteAccessAware
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
    private array $siteaccessList;

    private TranslatorInterface $translator;

    private ?SiteAccess $currentSiteaccess;

    public function __construct(
        IOServiceInterface $binaryFileIOService,
        Filesystem $fileSystem,
        Environment $twig,
        ConfigResolverInterface $configResolver,
        TranslatorInterface $translator,
        array $siteaccessList = []
    ) {
        $this->binaryfileIOService = $binaryFileIOService;
        $this->fileSystem = $fileSystem;
        $this->twig = $twig;
        $this->configResolver = $configResolver;
        $this->translator = $translator;
        $this->siteaccessList = $siteaccessList;
    }

    /**
     * @required
     */
    public function setSiteAccess(SiteAccess $siteAccess = null)
    {
        $this->currentSiteaccess = $siteAccess;
    }

    /**
     * @throws InvalidArgumentException
     * @throws InvalidArgumentValue
     */
    public function createFileCluster(?string $siteaccess = null): string
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

    public function existFileCluster(?string $siteaccess = null): bool
    {
        return $this->binaryfileIOService->exists($this->getFileId($siteaccess));
    }

    public function isMaintenanceModeRunning(?string $siteaccess = null): bool
    {
        $this->assertMaintenanceEnabled($siteaccess);

        return $this->existFileCluster($siteaccess);
    }

    /**
     * @throws InvalidArgumentException
     * @throws InvalidArgumentValue
     * @throws NotFoundException
     */
    public function manageMaintenance(?string $siteaccess = null): void
    {
        $this->assertMaintenanceEnabled($siteaccess);
        $isExistFile = $this->isMaintenanceModeRunning($siteaccess);
        $isExistFile ? $this->maintenanceUnLock($siteaccess) : $this->maintenanceLock($siteaccess);
    }

    /**
     * @throws InvalidArgumentValue
     * @throws NotFoundException
     */
    public function maintenanceUnLock(?string $siteaccess = null): bool
    {
        if ($this->isMaintenanceModeRunning($siteaccess)) {
            $this->deleteFileCluster($siteaccess);

            return true;
        }

        return false;
    }

    /**
     * @throws InvalidArgumentException
     * @throws InvalidArgumentValue
     */
    public function maintenanceLock(?string $siteaccess = null): bool
    {
        if ($this->existFileCluster($siteaccess)) {
            return false;
        }
        $this->createFileCluster($siteaccess);

        return true;
    }

    public function getResponse(?int $status = null, ?string $siteaccess = null): Response
    {
        try {
            $content = $this->twig->render($this->getParameter('template', $siteaccess));
        } catch (Exception $exception) {
            $content = $this->translator->trans('maintenance.response.unexpected_error', [], self::CONFIG_NAMESPACE);
        }
        $response = new Response();
        $status = $status ?? Response::HTTP_SERVICE_UNAVAILABLE;
        if (null !== $status) {
            $response->setStatusCode($status);
        }

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

    public function isMaintenanceEnabled(?string $siteaccess = null): bool
    {
        if (true !== $this->hasParameter('enable', $siteaccess)) {
            return false;
        }

        return true === $this->getParameter('enable', $siteaccess);
    }

    public function assertMaintenanceEnabled(?string $siteaccess = null): void
    {
        if (true !== $this->isMaintenanceEnabled($siteaccess)) {
            throw new RuntimeException(
                $this->translator->trans('maintenance.disabled', [], self::CONFIG_NAMESPACE)
            );
        }
    }

    /**
     * @return mixed|null
     */
    private function getParameter(string $paramName, ?string $siteaccess = null)
    {
        if ($this->hasParameter($paramName, $siteaccess)) {
            return $this->configResolver->getParameter($paramName, self::CONFIG_NAMESPACE, $siteaccess);
        }

        return null;
    }

    private function hasParameter(string $paramName, ?string $siteaccess = null): bool
    {
        return true === $this->configResolver->hasParameter($paramName, self::CONFIG_NAMESPACE, $siteaccess);
    }

    private function getLockFile(?string $siteaccess = null): string
    {
        $this->assertMaintenanceEnabled($siteaccess);

        return (string) $this->getParameter('lock_file_id', $siteaccess);
    }

    private function getFileId(string $siteaccess = null): string
    {
        $lockFile = $this->getLockFile($siteaccess);

        return self::CONFIG_NAMESPACE.'/'.($siteaccess ?? $this->currentSiteaccess->name).'/'.basename($lockFile);
    }

    /**
     * @throws InvalidArgumentValue
     * @throws NotFoundException
     */
    private function deleteFileCluster(?string $siteaccess = null): bool
    {
        $binaryFileId = $this->getFileId($siteaccess);
        if ($this->binaryfileIOService->exists($binaryFileId)) {
            $this->binaryfileIOService->deleteBinaryFile($this->binaryfileIOService->loadBinaryFile($binaryFileId));
        }

        return true;
    }
}

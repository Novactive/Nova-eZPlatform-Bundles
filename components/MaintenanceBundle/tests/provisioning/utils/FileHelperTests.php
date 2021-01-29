<?php

namespace Novactive\NovaeZMaintenanceBundle\tests\utils;

use PHPUnit\Framework\TestCase;
use Novactive\NovaeZMaintenanceBundle\Helper\FileHelper;

class FileHelperTest extends TestCase
{
    private $fileHelper;

    public function __construct(FileHelper $fileHelper)
    {
        $this->fileHelper = $fileHelper;
    }

    public function testGetFileId()
    {

    }

    public function testExistFileCluster()
    {
        $siteaccess = 1;
        $result = $this->fileHelper->existFileCluster($siteaccess);
        $this->assertEquals($result, true);
    }

    public function testIsMaintenanceModeRunning()
    {
        $siteaccess = 1;
        $result = $this->fileHelper->isMaintenanceModeRunning($siteaccess);
        $this->assertEquals($result, true);
    }

    public function testCreateFileCluster()
    {
        $siteaccess = 1;
        $result = $this->fileHelper->createFileCluster($siteaccess);
        $this->assertEquals($result, true);
    }
}
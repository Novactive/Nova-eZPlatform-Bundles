<?php
/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <m.strukov@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Core;

use eZ\Publish\Core\IO\IOServiceInterface;
use Symfony\Component\Filesystem\Filesystem;

class IOService
{
    /**
     * @var IOServiceInterface
     */
    private $io;

    public function __construct(IOServiceInterface $io)
    {
        $this->io = $io;
    }

    public function saveFile(string $fileName, string $content): string
    {
        $fs = new Filesystem();

        $temporaryPath = tempnam(sys_get_temp_dir(), uniqid($fileName, true));
        $fs->dumpFile($temporaryPath, $content);
        $uploadedFileStruct     = $this->io->newBinaryCreateStructFromLocalFile($temporaryPath);
        $uploadedFileStruct->id = $fileName;
        $ioFile                 = $this->io->createBinaryFile($uploadedFileStruct);
        $fs->remove($temporaryPath);

        return $ioFile->id;
    }

    public function readFile(string $filename): string
    {
        $file = $this->io->loadBinaryFile($filename);

        return $this->io->getFileContents($file);
    }

    public function fileExists(string $filename): bool
    {
        return $this->io->exists($filename);
    }

    public function cleanDir(string $dirPath): void
    {
        $this->io->deleteDirectory($dirPath);
    }
}

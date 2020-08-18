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

namespace Novactive\Bundle\eZMailingBundle\Core\DataHandler;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserImport.
 */
class UserImport
{
    /**
     * @var File
     * @Assert\NotBlank()
     * @Assert\File(
     *     mimeTypes={"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"},
     *     mimeTypesMessage="Please upload a valid excel file (xls, xlsx)"
     * )
     */
    private $file;

    /**
     * @return File
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @param File $file
     *
     * @return $this
     */
    public function setFile(File $file): self
    {
        $this->file = $file;

        return $this;
    }
}

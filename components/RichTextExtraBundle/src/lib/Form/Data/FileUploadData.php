<?php

namespace AlmaviaCX\Bundle\IbexaRichTextExtra\Form\Data;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class FileUploadData
{
    /**
     * @Assert\NotBlank()
     * @Assert\File()
     *
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    private $file;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $languageCode;

    public function __construct(?UploadedFile $file = null, string $languageCode = null)
    {
        $this->file = $file;
        $this->languageCode = $languageCode;
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * @return \AlmaviaCX\Bundle\IbexaRichTextExtra\Form\Data\FileUploadData
     */
    public function setFile(?UploadedFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getLanguageCode(): ?string
    {
        return $this->languageCode;
    }

    /**
     * @return \AlmaviaCX\Bundle\IbexaRichTextExtra\Form\Data\FileUploadData
     */
    public function setLanguageCode(?string $languageCode): self
    {
        $this->languageCode = $languageCode;

        return $this;
    }
}

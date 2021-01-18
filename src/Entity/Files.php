<?php

namespace App\Entity;

use App\Repository\FilesRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=FilesRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Files
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez choisir un fichier !")
     * @Assert\File(mimeTypes={ "application/pdf", "image/jpeg", "image/png"})
     */
    private $file;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity=CustomerFiles::class, inversedBy="files_id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $customerFiles;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity=ClientStatutDocument::class, inversedBy="files")
     */
    private $document;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */

    public function updatedTimestamps(): void
    {
        $dateTimeNow = new DateTime('now');

        $this->setUpdatedAt($dateTimeNow);

        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt($dateTimeNow);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile(){
        return $this->file;
    }

    public function setFile($file)
    {
        is_array($file) && $file = $file[0];
        
        $this->file = $file;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCustomerFiles(): ?CustomerFiles
    {
        return $this->customerFiles;
    }

    public function setCustomerFiles(?CustomerFiles $customerFiles): self
    {
        $this->customerFiles = $customerFiles;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getDocument(): ?ClientStatutDocument
    {
        return $this->document;
    }

    public function setDocument(?ClientStatutDocument $document): self
    {
        $this->document = $document;

        return $this;
    }
}

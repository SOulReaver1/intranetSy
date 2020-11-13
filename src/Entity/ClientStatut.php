<?php

namespace App\Entity;

use App\Repository\ClientStatutRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity(repositoryClass=ClientStatutRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class ClientStatut
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity=CustomerFiles::class, mappedBy="client_statut_id")
     */
    private $customerFiles;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\ManyToMany(targetEntity=ClientStatutDocument::class, mappedBy="client_statut")
     */
    private $clientStatutDocuments;


    public function __construct()
    {
        $this->customerFiles = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->clientStatutDocuments = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection|ClientStatutDocument[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(ClientStatutDocument $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
        }

        return $this;
    }

    public function removeFile(ClientStatutDocument $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
        }

        return $this;
    }

    /**
     * @return Collection|ClientStatutDocument[]
     */
    public function getClientStatutDocuments(): Collection
    {
        return $this->clientStatutDocuments;
    }

    public function addClientStatutDocument(ClientStatutDocument $clientStatutDocument): self
    {
        if (!$this->clientStatutDocuments->contains($clientStatutDocument)) {
            $this->clientStatutDocuments[] = $clientStatutDocument;
            $clientStatutDocument->addClientStatut($this);
        }

        return $this;
    }

    public function removeClientStatutDocument(ClientStatutDocument $clientStatutDocument): self
    {
        if ($this->clientStatutDocuments->contains($clientStatutDocument)) {
            $this->clientStatutDocuments->removeElement($clientStatutDocument);
            $clientStatutDocument->removeClientStatut($this);
        }

        return $this;
    }
}

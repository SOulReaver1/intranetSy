<?php

namespace App\Entity;

use App\Repository\ClientStatutDocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\ClientStatut;
use DateTime;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ClientStatutDocumentRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="name", message="Le nom du document existe déjà !")
 */
class ClientStatutDocument
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\ManyToMany(targetEntity=ClientStatut::class, inversedBy="clientStatutDocuments")
     */
    private $client_statut;

    /**
     * @ORM\OneToMany(targetEntity=Files::class, mappedBy="document")
     */
    private $files;

    public function __construct()
    {
        $this->client_statut = new ArrayCollection();
        $this->files = new ArrayCollection();
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

    public function __toString()
    {
        return $this->name;
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
     * @return Collection|ClientStatut[]
     */
    public function getClientStatut(): Collection
    {
        return $this->client_statut;
    }

    public function addClientStatut(ClientStatut $clientStatut): self
    {
        if (!$this->client_statut->contains($clientStatut)) {
            $this->client_statut[] = $clientStatut;
        }

        return $this;
    }

    public function removeClientStatut(ClientStatut $clientStatut): self
    {
        if ($this->client_statut->contains($clientStatut)) {
            $this->client_statut->removeElement($clientStatut);
        }

        return $this;
    }

    /**
     * @return Collection|Files[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(Files $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setDocument($this);
        }

        return $this;
    }

    public function removeFile(Files $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
            // set the owning side to null (unless already changed)
            if ($file->getDocument() === $this) {
                $file->setDocument(null);
            }
        }

        return $this;
    }
}

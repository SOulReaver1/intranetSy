<?php

namespace App\Entity;

use App\Repository\GlobalStatutRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GlobalStatutRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class GlobalStatut
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\OneToMany(targetEntity=CustomerFiles::class, mappedBy="global_statut")
     */
    private $customerFiles;

    /**
     * @ORM\OneToMany(targetEntity=CustomerFilesStatut::class, mappedBy="global_statut")
     */
    private $customerFilesStatuts;

    /**
     * @ORM\OneToMany(targetEntity=TicketStatut::class, mappedBy="global_statut")
     */
    private $ticketStatuts;

    public function __construct()
    {
        $this->customerFiles = new ArrayCollection();
        $this->customerFilesStatuts = new ArrayCollection();
        $this->ticketStatuts = new ArrayCollection();
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
     * @return Collection|CustomerFiles[]
     */
    public function getCustomerFiles(): Collection
    {
        return $this->customerFiles;
    }

    public function addCustomerFile(CustomerFiles $customerFile): self
    {
        if (!$this->customerFiles->contains($customerFile)) {
            $this->customerFiles[] = $customerFile;
            $customerFile->setGlobalStatut($this);
        }

        return $this;
    }

    public function removeCustomerFile(CustomerFiles $customerFile): self
    {
        if ($this->customerFiles->removeElement($customerFile)) {
            // set the owning side to null (unless already changed)
            if ($customerFile->getGlobalStatut() === $this) {
                $customerFile->setGlobalStatut(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CustomerFilesStatut[]
     */
    public function getCustomerFilesStatuts(): Collection
    {
        return $this->customerFilesStatuts;
    }

    public function addCustomerFilesStatut(CustomerFilesStatut $customerFilesStatut): self
    {
        if (!$this->customerFilesStatuts->contains($customerFilesStatut)) {
            $this->customerFilesStatuts[] = $customerFilesStatut;
            $customerFilesStatut->setGlobalStatut($this);
        }

        return $this;
    }

    public function removeCustomerFilesStatut(CustomerFilesStatut $customerFilesStatut): self
    {
        if ($this->customerFilesStatuts->removeElement($customerFilesStatut)) {
            // set the owning side to null (unless already changed)
            if ($customerFilesStatut->getGlobalStatut() === $this) {
                $customerFilesStatut->setGlobalStatut(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TicketStatut[]
     */
    public function getTicketStatuts(): Collection
    {
        return $this->ticketStatuts;
    }

    public function addTicketStatut(TicketStatut $ticketStatut): self
    {
        if (!$this->ticketStatuts->contains($ticketStatut)) {
            $this->ticketStatuts[] = $ticketStatut;
            $ticketStatut->setGlobalStatut($this);
        }

        return $this;
    }

    public function removeTicketStatut(TicketStatut $ticketStatut): self
    {
        if ($this->ticketStatuts->removeElement($ticketStatut)) {
            // set the owning side to null (unless already changed)
            if ($ticketStatut->getGlobalStatut() === $this) {
                $ticketStatut->setGlobalStatut(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\ProviderRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProviderRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Provider
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
     * @ORM\OneToMany(targetEntity=ProviderProduct::class, mappedBy="provider")
     */
    private $providerProducts;

    public function __construct()
    {
        $this->providerProducts = new ArrayCollection();
        $this->providerParams = new ArrayCollection();
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
     * @return Collection|ProviderProduct[]
     */
    public function getProviderProducts(): Collection
    {
        return $this->providerProducts;
    }

    public function addProviderProduct(ProviderProduct $providerProduct): self
    {
        if (!$this->providerProducts->contains($providerProduct)) {
            $this->providerProducts[] = $providerProduct;
            $providerProduct->setProvider($this);
        }

        return $this;
    }

    public function removeProviderProduct(ProviderProduct $providerProduct): self
    {
        if ($this->providerProducts->contains($providerProduct)) {
            $this->providerProducts->removeElement($providerProduct);
            // set the owning side to null (unless already changed)
            if ($providerProduct->getProvider() === $this) {
                $providerProduct->setProvider(null);
            }
        }

        return $this;
    }
}

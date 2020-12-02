<?php

namespace App\Entity;

use App\Repository\ProviderParamRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProviderParamRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class ProviderParam
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
     * @ORM\ManyToMany(targetEntity=ProviderProduct::class, mappedBy="params")
     */
    private $providerProducts;

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

    public function __construct()
    {
        $this->providerProducts = new ArrayCollection();
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
            $providerProduct->addParam($this);
        }

        return $this;
    }

    public function removeProviderProduct(ProviderProduct $providerProduct): self
    {
        if ($this->providerProducts->contains($providerProduct)) {
            $this->providerProducts->removeElement($providerProduct);
            $providerProduct->removeParam($this);
        }

        return $this;
    }
}

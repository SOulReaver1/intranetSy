<?php

namespace App\Entity;

use App\Repository\ProviderProductRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProviderProductRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class ProviderProduct
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
     * @ORM\ManyToOne(targetEntity=Provider::class, inversedBy="providerProducts")
     */
    private $provider;

    /**
     * @ORM\ManyToMany(targetEntity=ProviderParam::class, inversedBy="providerProducts")
     */
    private $params;

    /**
     * @ORM\OneToMany(targetEntity=CustomerFiles::class, mappedBy="product")
     */
    private $customerFiles;

    public function __construct()
    {
        $this->params = new ArrayCollection();
        $this->customerFiles = new ArrayCollection();
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

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return Collection|ProviderParam[]
     */
    public function getParams(): Collection
    {
        return $this->params;
    }

    public function addParam(ProviderParam $param): self
    {
        if (!$this->params->contains($param)) {
            $this->params[] = $param;
        }

        return $this;
    }

    public function removeParam(ProviderParam $param): self
    {
        if ($this->params->contains($param)) {
            $this->params->removeElement($param);
        }

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
            $customerFile->setProduct($this);
        }

        return $this;
    }

    public function removeCustomerFile(CustomerFiles $customerFile): self
    {
        if ($this->customerFiles->contains($customerFile)) {
            $this->customerFiles->removeElement($customerFile);
            // set the owning side to null (unless already changed)
            if ($customerFile->getProduct() === $this) {
                $customerFile->setProduct(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\SmsAutoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SmsAutoRepository::class)
 */
class SmsAuto
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
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $step;

    /**
     * @ORM\OneToMany(targetEntity=Sms::class, mappedBy="step")
     */
    private $sms;

    public function __construct()
    {
        $this->sms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getStep(): ?int
    {
        return $this->step;
    }

    public function setStep(int $step): self
    {
        $this->step = $step;

        return $this;
    }

    /**
     * @return Collection|Sms[]
     */
    public function getSms(): Collection
    {
        return $this->sms;
    }

    public function addSms(Sms $sms): self
    {
        if (!$this->sms->contains($sms)) {
            $this->sms[] = $sms;
            $sms->setStep($this);
        }

        return $this;
    }

    public function removeSms(Sms $sms): self
    {
        if ($this->sms->contains($sms)) {
            $this->sms->removeElement($sms);
            // set the owning side to null (unless already changed)
            if ($sms->getStep() === $this) {
                $sms->setStep(null);
            }
        }

        return $this;
    }
}

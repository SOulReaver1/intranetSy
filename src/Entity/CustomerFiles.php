<?php

namespace App\Entity;

use App\Repository\CustomerFilesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CustomerFilesRepository::class)
 */
class CustomerFiles
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
    private $Sexe;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Adresse;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="integer")
     */
    private $zip_code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $home_phone;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $cellphone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $referent_name;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $referent_phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $referent_statut;

    /**
     * @ORM\ManyToOne(targetEntity=CustomerFilesStatut::class, inversedBy="customerFiles")
     */
    private $customer_statut;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="customerFiles")
     */
    private $users_id;

    /**
     * @ORM\ManyToOne(targetEntity=ClientStatut::class, inversedBy="customerFiles")
     */
    private $client_statut_id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $stairs;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mail_al;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password_al;

    /**
     * @ORM\Column(type="boolean")
     */
    private $annex_quote;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $annex_quote_description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $annex_quote_commentary;

    /**
     * @ORM\OneToMany(targetEntity=Files::class, mappedBy="customerFiles")
     */
    private $files_id;

    /**
     * @ORM\ManyToOne(targetEntity=CustomerSource::class, inversedBy="customerFiles")
     */
    private $customer_source;

    /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="customer_file")
     */
    private $tickets;

    public function __construct()
    {
        $this->users_id = new ArrayCollection();
        $this->files_id = new ArrayCollection();
        $this->tickets = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->Name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSexe(): ?string
    {
        return $this->Sexe;
    }

    public function setSexe(string $Sexe): self
    {
        $this->Sexe = $Sexe;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->Adresse;
    }

    public function setAdresse(string $Adresse): self
    {
        $this->Adresse = $Adresse;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getZipCode(): ?int
    {
        return $this->zip_code;
    }

    public function setZipCode(int $zip_code): self
    {
        $this->zip_code = $zip_code;

        return $this;
    }

    public function getHomePhone(): ?string
    {
        return $this->home_phone;
    }

    public function setHomePhone(string $home_phone): self
    {
        $this->home_phone = $home_phone;

        return $this;
    }

    public function getCellphone(): ?string
    {
        return $this->cellphone;
    }

    public function setCellphone(?string $cellphone): self
    {
        $this->cellphone = $cellphone;

        return $this;
    }

    public function getReferentName(): ?string
    {
        return $this->referent_name;
    }

    public function setReferentName(string $referent_name): self
    {
        $this->referent_name = $referent_name;

        return $this;
    }

    public function getReferentPhone(): ?string
    {
        return $this->referent_phone;
    }

    public function setReferentPhone(?string $referent_phone): self
    {
        $this->referent_phone = $referent_phone;

        return $this;
    }

    public function getReferentStatut(): ?string
    {
        return $this->referent_statut;
    }

    public function setReferentStatut(?string $referent_statut): self
    {
        $this->referent_statut = $referent_statut;

        return $this;
    }

    public function getCustomerStatut(): ?CustomerFilesStatut
    {
        return $this->customer_statut;
    }

    public function setCustomerStatut(?CustomerFilesStatut $customer_statut): self
    {
        $this->customer_statut = $customer_statut;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsersId(): Collection
    {
        return $this->users_id;
    }

    public function addUsersId(User $usersId): self
    {
        if (!$this->users_id->contains($usersId)) {
            $this->users_id[] = $usersId;
        }

        return $this;
    }

    public function removeUsersId(User $usersId): self
    {
        if ($this->users_id->contains($usersId)) {
            $this->users_id->removeElement($usersId);
        }

        return $this;
    }

    public function getClientStatutId(): ?ClientStatut
    {
        return $this->client_statut_id;
    }

    public function setClientStatutId(?ClientStatut $client_statut_id): self
    {
        $this->client_statut_id = $client_statut_id;

        return $this;
    }

    public function getStairs(): ?bool
    {
        return $this->stairs;
    }

    public function setStairs(bool $stairs): self
    {
        $this->stairs = $stairs;

        return $this;
    }

    public function getMailAl(): ?string
    {
        return $this->mail_al;
    }

    public function setMailAl(?string $mail_al): self
    {
        $this->mail_al = $mail_al;

        return $this;
    }

    public function getPasswordAl(): ?string
    {
        return $this->password_al;
    }

    public function setPasswordAl(?string $password_al): self
    {
        $this->password_al = $password_al;

        return $this;
    }

    public function getAnnexQuote(): ?bool
    {
        return $this->annex_quote;
    }

    public function setAnnexQuote(bool $annex_quote): self
    {
        $this->annex_quote = $annex_quote;

        return $this;
    }

    public function getAnnexQuoteDescription(): ?string
    {
        return $this->annex_quote_description;
    }

    public function setAnnexQuoteDescription(?string $annex_quote_description): self
    {
        $this->annex_quote_description = $annex_quote_description;

        return $this;
    }

    public function getAnnexQuoteCommentary(): ?string
    {
        return $this->annex_quote_commentary;
    }

    public function setAnnexQuoteCommentary(?string $annex_quote_commentary): self
    {
        $this->annex_quote_commentary = $annex_quote_commentary;

        return $this;
    }

    /**
     * @return Collection|Files[]
     */
    public function getFilesId(): Collection
    {
        return $this->files_id;
    }

    public function addFilesId(Files $filesId): self
    {
        if (!$this->files_id->contains($filesId)) {
            $this->files_id[] = $filesId;
            $filesId->setCustomerFiles($this);
        }

        return $this;
    }

    public function removeFilesId(Files $filesId): self
    {
        if ($this->files_id->contains($filesId)) {
            $this->files_id->removeElement($filesId);
            // set the owning side to null (unless already changed)
            if ($filesId->getCustomerFiles() === $this) {
                $filesId->setCustomerFiles(null);
            }
        }

        return $this;
    }

    public function getCustomerSource(): ?CustomerSource
    {
        return $this->customer_source;
    }

    public function setCustomerSource(?CustomerSource $customer_source): self
    {
        $this->customer_source = $customer_source;

        return $this;
    }

    /**
     * @return Collection|Ticket[]
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets[] = $ticket;
            $ticket->setCustomerFile($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->contains($ticket)) {
            $this->tickets->removeElement($ticket);
            // set the owning side to null (unless already changed)
            if ($ticket->getCustomerFile() === $this) {
                $ticket->setCustomerFile(null);
            }
        }

        return $this;
    }
}

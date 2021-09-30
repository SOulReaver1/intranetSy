<?php

namespace App\Entity;

use App\Repository\CustomerFilesRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CustomerFilesRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class CustomerFiles
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"show"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sexe;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"show"})
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"show"})
     */
    private $city;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"show"})
     */
    private $zip_code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"show"})
     */
    private $home_phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"show"})
     */
    private $cellphone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Groups({"show"})
     */
    private $customer_statut;

    /**
     * @ORM\ManyToOne(targetEntity=ClientStatut::class, inversedBy="customerFiles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client_statut;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $stairs = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"show"})
     */
    private $mail_al;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"show"})
     */
    private $password_al;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $annex_quote;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $commentary;

    /**
     * @ORM\OneToMany(targetEntity=Files::class, mappedBy="customerFiles")
     * @ORM\JoinColumn(nullable=true)
     */
    private $files;

    /**
     * @ORM\ManyToOne(targetEntity=CustomerSource::class, inversedBy="customerFiles")
     * @ORM\JoinColumn(nullable=true)
     */
    private $customer_source;

    /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="customer_file")
     */
    private $tickets;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $route_number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address_complement;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $lng;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $lat;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="customerFiles")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $installer;

    /**
     * @ORM\ManyToOne(targetEntity=ProviderProduct::class, inversedBy="customerFiles")
     */
    private $product;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $acompte;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $solde;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $invoice_number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dossier_number;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_depot;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_cmd_materiel;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_install;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"show"})
     */
    private $date_expertise;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_footage;

    /**
     * @ORM\Column(type="boolean")
     */
    private $messageSend = false;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="customerFileCreated")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $created_by;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="customerFileMetreur")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $metreur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity=GlobalStatut::class, inversedBy="customerFiles")
     */
    private $global_statut;

    /**
     * @ORM\OneToMany(targetEntity=Sms::class, mappedBy="customer_file")
     */
    private $sms;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

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

    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->tickets = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

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

    public function getClientStatut(): ?ClientStatut
    {
        return $this->client_statut;
    }

    public function setClientStatut(?ClientStatut $client_statut): self
    {
        $this->client_statut = $client_statut;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCommentary(): ?string
    {
        return $this->commentary;
    }

    public function setCommentary(?string $commentary): self
    {
        $this->commentary = $commentary;

        return $this;
    }

    /**
     * @return Collection|Files[]
     */
    public function getFilesId(): Collection
    {
        return $this->files;
    }

    public function addFilesId(Files $filesId): self
    {
        if (!$this->files->contains($filesId)) {
            $this->files[] = $filesId;
            $filesId->setCustomerFiles($this);
        }

        return $this;
    }

    public function removeFilesId(Files $filesId): self
    {
        if ($this->files->contains($filesId)) {
            $this->files->removeElement($filesId);
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

    public function getRouteNumber(): ?int
    {
        return $this->route_number;
    }

    public function setRouteNumber(int $route_number): self
    {
        $this->route_number = $route_number;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getAddressComplement(): ?string
    {
        return $this->address_complement;
    }

    public function setAddressComplement(?string $address_complement): self
    {
        $this->address_complement = $address_complement;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(?float $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): self
    {
        $this->lat = $lat;

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

    public function getInstaller(): ?User
    {
        return $this->installer;
    }

    public function setInstaller(?User $installer): self
    {
        $this->installer = $installer;

        return $this;
    }

    public function getProduct(): ?ProviderProduct
    {
        return $this->product;
    }

    public function setProduct(?ProviderProduct $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getAcompte(): ?\DateTimeInterface
    {
        return $this->acompte;
    }

    public function setAcompte(?\DateTimeInterface $acompte): self
    {
        $this->acompte = $acompte;

        return $this;
    }

    public function getSolde(): ?\DateTimeInterface
    {
        return $this->solde;
    }

    public function setSolde(?\DateTimeInterface $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoice_number;
    }

    public function setInvoiceNumber(?string $invoice_number): self
    {
        $this->invoice_number = $invoice_number;

        return $this;
    }

    public function getDossierNumber(): ?string
    {
        return $this->dossier_number;
    }

    public function setDossierNumber(?string $dossier_number): self
    {
        $this->dossier_number = $dossier_number;

        return $this;
    }

    public function getDateDepot(): ?\DateTimeInterface
    {
        return $this->date_depot;
    }

    public function setDateDepot(?\DateTimeInterface $date_depot): self
    {
        $this->date_depot = $date_depot;

        return $this;
    }

    public function getDateCmdMateriel(): ?\DateTimeInterface
    {
        return $this->date_cmd_materiel;
    }

    public function setDateCmdMateriel(?\DateTimeInterface $date_cmd_materiel): self
    {
        $this->date_cmd_materiel = $date_cmd_materiel;

        return $this;
    }

    public function getDateInstall(): ?\DateTimeInterface
    {
        return $this->date_install;
    }

    public function setDateInstall(?\DateTimeInterface $date_install): self
    {
        $this->date_install = $date_install;

        return $this;
    }

    public function getDateExpertise(): ?\DateTimeInterface
    {
        return $this->date_expertise;
    }

    public function setDateExpertise(?\DateTimeInterface $date_expertise): self
    {
        $this->date_expertise = $date_expertise;

        return $this;
    }

    public function getDateFootage(): ?\DateTimeInterface
    {
        return $this->date_footage;
    }

    public function setDateFootage(?\DateTimeInterface $date_footage): self
    {
        $this->date_footage = $date_footage;

        return $this;
    }

    public function getMessageSend(): ?bool
    {
        return $this->messageSend;
    }

    public function setMessageSend(bool $messageSend): self
    {
        $this->messageSend = $messageSend;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->created_by;
    }

    public function setCreatedBy(?User $created_by): self
    {
        $this->created_by = $created_by;

        return $this;
    }

    public function getMetreur(): ?User
    {
        return $this->metreur;
    }

    public function setMetreur(?User $metreur): self
    {
        $this->metreur = $metreur;

        return $this;
    }

    public function getMessage(): ?bool
    {
        return $this->message;
    }

    public function setMessage(bool $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getGlobalStatut(): ?GlobalStatut
    {
        return $this->global_statut;
    }

    public function setGlobalStatut(?GlobalStatut $global_statut): self
    {
        $this->global_statut = $global_statut;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

}

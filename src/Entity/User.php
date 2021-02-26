<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\CustomerFiles;
use App\Repository\NotificationRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="Un compte est déjà associer avec cette email !")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="creator")
     */
    private $tickets;

    /**
     * @ORM\ManyToMany(targetEntity=Notification::class, mappedBy="users")
     */
    private $notifications;

    /**
     * @ORM\OneToMany(targetEntity=Help::class, mappedBy="user_id")
     */
    private $helps;

    /**
     * @ORM\OneToMany(targetEntity=TicketMessage::class, mappedBy="from_user")
     */
    private $ticketMessages;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=CustomerFiles::class, mappedBy="installer")
     */
    private $customerFiles;

    /**
     * @ORM\ManyToMany(targetEntity=Ticket::class, mappedBy="users")
     */
    private $ticket_inside;

    /**
     * @ORM\OneToMany(targetEntity=CustomerFiles::class, mappedBy="created_by")
     */
    private $customerFileCreated;

    /**
     * @ORM\OneToMany(targetEntity=CustomerFiles::class, mappedBy="metreur")
     */
    private $customerFileMetreur;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->customerFiles = new ArrayCollection();
        $this->helps = new ArrayCollection();
        $this->ticketMessages = new ArrayCollection();
        $this->ticket_inside = new ArrayCollection();
        $this->customerFileCreated = new ArrayCollection();
        $this->customerFileMetreur = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->username.' - '.$this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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
            $ticket->setCreator($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->contains($ticket)) {
            $this->tickets->removeElement($ticket);
            // set the owning side to null (unless already changed)
            if ($ticket->getCreator() === $this) {
                $ticket->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->addUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            $notification->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|Help[]
     */
    public function getHelps(): Collection
    {
        return $this->helps;
    }

    public function addHelp(Help $help): self
    {
        if (!$this->helps->contains($help)) {
            $this->helps[] = $help;
            $help->setUserId($this);
        }

        return $this;
    }

    public function removeHelp(Help $help): self
    {
        if ($this->helps->contains($help)) {
            $this->helps->removeElement($help);
            // set the owning side to null (unless already changed)
            if ($help->getUserId() === $this) {
                $help->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TicketMessage[]
     */
    public function getTicketMessages(): Collection
    {
        return $this->ticketMessages;
    }

    public function addTicketMessage(TicketMessage $ticketMessage): self
    {
        if (!$this->ticketMessages->contains($ticketMessage)) {
            $this->ticketMessages[] = $ticketMessage;
            $ticketMessage->setFromUser($this);
        }

        return $this;
    }

    public function removeTicketMessage(TicketMessage $ticketMessage): self
    {
        if ($this->ticketMessages->contains($ticketMessage)) {
            $this->ticketMessages->removeElement($ticketMessage);
            // set the owning side to null (unless already changed)
            if ($ticketMessage->getFromUser() === $this) {
                $ticketMessage->setFromUser(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

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
            $customerFile->setInstaller($this);
        }

        return $this;
    }

    public function removeCustomerFile(CustomerFiles $customerFile): self
    {
        if ($this->customerFiles->contains($customerFile)) {
            $this->customerFiles->removeElement($customerFile);
            // set the owning side to null (unless already changed)
            if ($customerFile->getInstaller() === $this) {
                $customerFile->setInstaller(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ticket[]
     */
    public function getTicketInside(): Collection
    {
        return $this->ticket_inside;
    }

    public function addTicketInside(Ticket $ticketInside): self
    {
        if (!$this->ticket_inside->contains($ticketInside)) {
            $this->ticket_inside[] = $ticketInside;
            $ticketInside->addUser($this);
        }

        return $this;
    }

    public function removeTicketInside(Ticket $ticketInside): self
    {
        if ($this->ticket_inside->contains($ticketInside)) {
            $this->ticket_inside->removeElement($ticketInside);
            $ticketInside->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|CustomerFiles[]
     */
    public function getCustomerFileCreated(): Collection
    {
        return $this->customerFileCreated;
    }

    public function addCustomerFileCreated(CustomerFiles $customerFileCreated): self
    {
        if (!$this->customerFileCreated->contains($customerFileCreated)) {
            $this->customerFileCreated[] = $customerFileCreated;
            $customerFileCreated->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCustomerFileCreated(CustomerFiles $customerFileCreated): self
    {
        if ($this->customerFileCreated->contains($customerFileCreated)) {
            $this->customerFileCreated->removeElement($customerFileCreated);
            // set the owning side to null (unless already changed)
            if ($customerFileCreated->getCreatedBy() === $this) {
                $customerFileCreated->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CustomerFiles[]
     */
    public function getCustomerFileMetreur(): Collection
    {
        return $this->customerFileMetreur;
    }

    public function addCustomerFileMetreur(CustomerFiles $customerFileMetreur): self
    {
        if (!$this->customerFileMetreur->contains($customerFileMetreur)) {
            $this->customerFileMetreur[] = $customerFileMetreur;
            $customerFileMetreur->setMetreur($this);
        }

        return $this;
    }

    public function removeCustomerFileMetreur(CustomerFiles $customerFileMetreur): self
    {
        if ($this->customerFileMetreur->contains($customerFileMetreur)) {
            $this->customerFileMetreur->removeElement($customerFileMetreur);
            // set the owning side to null (unless already changed)
            if ($customerFileMetreur->getMetreur() === $this) {
                $customerFileMetreur->setMetreur(null);
            }
        }

        return $this;
    }
}

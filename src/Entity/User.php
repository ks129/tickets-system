<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var string|null The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column]
    private bool $isAdmin = false;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'hosts')]
    private Collection $eventsHosts;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'ticketCheckers')]
    private Collection $eventsCheckers;

    public function __construct()
    {
        $this->eventsHosts = new ArrayCollection();
        $this->eventsCheckers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];
        if ($this->isAdmin) {
            $roles[] = 'ROLE_ADMIN';
        }

        return array_unique($roles);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function isAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setAdmin(bool $isAdmin): static
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEventsHost(): Collection
    {
        return $this->eventsHosts;
    }

    public function addEventsHost(Event $eventsHost): static
    {
        if (!$this->eventsHosts->contains($eventsHost)) {
            $this->eventsHosts->add($eventsHost);
            $eventsHost->addHost($this);
        }

        return $this;
    }

    public function removeEventsHost(Event $eventsHost): static
    {
        if ($this->eventsHosts->removeElement($eventsHost)) {
            $eventsHost->removeHost($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEventsCheckers(): Collection
    {
        return $this->eventsCheckers;
    }

    public function addEventsChecker(Event $eventsChecker): static
    {
        if (!$this->eventsCheckers->contains($eventsChecker)) {
            $this->eventsCheckers->add($eventsChecker);
            $eventsChecker->addTicketChecker($this);
        }

        return $this;
    }

    public function removeEventsChecker(Event $eventsChecker): static
    {
        if ($this->eventsCheckers->removeElement($eventsChecker)) {
            $eventsChecker->removeTicketChecker($this);
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $beginAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $endAt = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\JoinTable(name: 'event_user_host')]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: User::class)]
    private Collection $hosts;

    /**
     * @var Collection<int, User>
     */
    #[ORM\JoinTable(name: 'event_user_checker')]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'eventsCheckers')]
    private Collection $ticketCheckers;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    /**
     * @var Collection<int, EventNews>
     */
    #[ORM\OneToMany(targetEntity: EventNews::class, mappedBy: 'event', orphanRemoval: true)]
    private Collection $eventNews;

    /**
     * @var Collection<int, EventTicketType>
     */
    #[ORM\OneToMany(targetEntity: EventTicketType::class, mappedBy: 'event', orphanRemoval: true)]
    private Collection $eventTicketTypes;

    public function __construct()
    {
        $this->hosts = new ArrayCollection();
        $this->ticketCheckers = new ArrayCollection();
        $this->eventNews = new ArrayCollection();
        $this->eventTicketTypes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBeginAt(): ?\DateTimeImmutable
    {
        return $this->beginAt;
    }

    public function setBeginAt(\DateTimeImmutable $beginAt): static
    {
        $this->beginAt = $beginAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeImmutable $endAt): static
    {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getHosts(): Collection
    {
        return $this->hosts;
    }

    public function addHost(User $host): static
    {
        if (!$this->hosts->contains($host)) {
            $this->hosts->add($host);
        }

        return $this;
    }

    public function removeHost(User $host): static
    {
        $this->hosts->removeElement($host);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getTicketCheckers(): Collection
    {
        return $this->ticketCheckers;
    }

    public function addTicketChecker(User $ticketChecker): static
    {
        if (!$this->ticketCheckers->contains($ticketChecker)) {
            $this->ticketCheckers->add($ticketChecker);
        }

        return $this;
    }

    public function removeTicketChecker(User $ticketChecker): static
    {
        $this->ticketCheckers->removeElement($ticketChecker);

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, EventNews>
     */
    public function getEventNews(): Collection
    {
        return $this->eventNews;
    }

    public function addEventNews(EventNews $eventNews): static
    {
        if (!$this->eventNews->contains($eventNews)) {
            $this->eventNews->add($eventNews);
            $eventNews->setEvent($this);
        }

        return $this;
    }

    public function removeEventNews(EventNews $eventNews): static
    {
        if ($this->eventNews->removeElement($eventNews)) {
            // set the owning side to null (unless already changed)
            if ($eventNews->getEvent() === $this) {
                $eventNews->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EventTicketType>
     */
    public function getEventTicketTypes(): Collection
    {
        return $this->eventTicketTypes;
    }

    public function addEventTicketType(EventTicketType $eventTicketType): static
    {
        if (!$this->eventTicketTypes->contains($eventTicketType)) {
            $this->eventTicketTypes->add($eventTicketType);
            $eventTicketType->setEvent($this);
        }

        return $this;
    }

    public function removeEventTicketType(EventTicketType $eventTicketType): static
    {
        if ($this->eventTicketTypes->removeElement($eventTicketType)) {
            // set the owning side to null (unless already changed)
            if ($eventTicketType->getEvent() === $this) {
                $eventTicketType->setEvent(null);
            }
        }

        return $this;
    }
}

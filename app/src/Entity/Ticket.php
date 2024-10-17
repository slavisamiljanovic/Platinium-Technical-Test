<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use App\Config\TicketConfig;
use App\Repository\TicketRepository;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ORM\Table(name: 'tickets')]
class Ticket
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(
        length: TicketConfig::NAME_LENGTH,
    )]
    private string $name;

    #[ORM\Column(
        length: TicketConfig::DESCRIPTION_LENGTH,
        nullable: true,
    )]
    private ?string $description = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $createdAt;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $updatedAt;

    /**
     * @var ArrayCollection<int, Event>
     */
    #[ORM\ManyToMany(
        targetEntity: Event::class,
        inversedBy: 'tickets',
        cascade: ['persist']
    )]
    #[ORM\JoinTable(
        name: 'ticket_events',
        joinColumns: [
            new ORM\JoinColumn(name: 'ticket_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')
        ],
        inverseJoinColumns: [
            new ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')
        ]
    )]
    private iterable $events;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getEvents(): iterable
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->addTicket($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            $event->removeTicket($this);
        }

        return $this;
    }

    #[ORM\PreRemove]
    public function removeEventRelations()
    {
        foreach ($this->events as $event) {
            $event->removeTicket($this);
        }
    }

}

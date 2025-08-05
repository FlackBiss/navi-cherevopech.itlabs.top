<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Dto\SessionsInput;
use App\Repository\SessionsRepository;
use App\State\SessionsProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionsRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            input: SessionsInput::class,
            processor: SessionsProcessor::class
        )
    ],
)]
class Sessions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $endAt = null;

    #[ORM\Column(nullable: true)]
    private ?int $deltaTime = null;

    #[ORM\Column(nullable: true)]
    private ?int $countEvents = 0;

    #[ORM\OneToMany(targetEntity: SessionEvents::class, mappedBy: 'session', cascade: ['persist', 'remove'])]
    private Collection $events;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    private ?Terminal $terminal = null;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(?\DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeImmutable $endAt): static
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getDeltaTime(): ?int
    {
        return $this->deltaTime;
    }

    public function setDeltaTime(?int $deltaTime): static
    {
        $this->deltaTime = $deltaTime;

        return $this;
    }

    public function getCountEvents(): ?int
    {
        return $this->countEvents;
    }

    public function setCountEvents(?int $countEvents): static
    {
        $this->countEvents = $countEvents;

        return $this;
    }

    /**
     * @return Collection<int, SessionEvents>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(SessionEvents $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setSession($this);
        }

        return $this;
    }

    public function removeEvent(SessionEvents $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getSession() === $this) {
                $event->setSession(null);
            }
        }

        return $this;
    }

    public function getTerminal(): ?Terminal
    {
        return $this->terminal;
    }

    public function setTerminal(?Terminal $terminal): static
    {
        $this->terminal = $terminal;

        return $this;
    }
}

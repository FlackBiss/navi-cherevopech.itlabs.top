<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\Area\AreaCreateController;
use App\Controller\Area\AreaUpdateController;
use App\Dto\AreaDto;
use App\Repository\AreaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            controller: AreaCreateController::class,
            input: AreaDto::class
        ),
        new Patch(
            controller: AreaUpdateController::class,
            input: AreaDto::class
        ),
        new Delete()
    ],
    normalizationContext: ['groups' => ['area:read']],
    paginationEnabled: false,
)]
#[ORM\Entity(repositoryClass: AreaRepository::class)]
class Area
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['area:read'])]
    private ?int $id = null;

    /**
     * @var Collection<int, Point>
     */
    #[ORM\ManyToMany(targetEntity: Point::class, inversedBy: 'areas', cascade: ['all'])]
    #[Groups(['area:read'])]
    private Collection $points;

    #[ORM\ManyToOne(inversedBy: 'areas')]
    #[Groups(['area:read'])]
    private ?Floor $floor = null;

    /**
     * @var Collection<int, Tenant>
     */
    #[ORM\OneToMany(targetEntity: Tenant::class, mappedBy: 'area')]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    private Collection $tenants;

    #[ORM\OneToMany(targetEntity: Terminal::class, mappedBy: 'area')]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    private Collection $terminals;

    public function __construct()
    {
        $this->points = new ArrayCollection();
        $this->tenants = new ArrayCollection();
        $this->terminals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Point>
     */
    public function getPoints(): Collection
    {
        return $this->points;
    }

    /**
     * @param Collection $points
     */
    public function setPoints(Collection $points): void
    {
        $this->points = $points;
    }

    public function addPoint(Point $point): static
    {
        if (!$this->points->contains($point)) {
            $this->points->add($point);
        }

        return $this;
    }

    public function removePoint(Point $point): static
    {
        $this->points->removeElement($point);

        return $this;
    }

    public function getFloor(): ?Floor
    {
        return $this->floor;
    }

    public function setFloor(?Floor $floor): static
    {
        $this->floor = $floor;

        return $this;
    }

    /**
     * @return Collection<int, Tenant>
     */
    public function getTenants(): Collection
    {
        return $this->tenants;
    }

    public function addTenants(Tenant $cabinets): static
    {
        if (!$this->tenants->contains($cabinets)) {
            $this->tenants->add($cabinets);
            $cabinets->setArea($this);
        }

        return $this;
    }

    public function removeTenants(Tenant $cabinets): static
    {
        if ($this->tenants->removeElement($cabinets)) {
            // set the owning side to null (unless already changed)
            if ($cabinets->getArea() === $this) {
                $cabinets->setArea(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Terminal>
     */
    public function getTerminals(): Collection
    {
        return $this->terminals;
    }

    public function addTerminal(Terminal $terminal): static
    {
        if (!$this->terminals->contains($terminal)) {
            $this->terminals->add($terminal);
            $terminal->setArea($this);
        }

        return $this;
    }

    public function removeTerminal(Terminal $terminal): static
    {
        if ($this->terminals->removeElement($terminal)) {
            // set the owning side to null (unless already changed)
            if ($terminal->getArea() === $this) {
                $terminal->setArea(null);
            }
        }

        return $this;
    }
}

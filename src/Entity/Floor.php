<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\UpdatedAtTrait;
use App\Repository\FloorRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
#[ApiResource(
    operations: [
        new Get(uriTemplate: '/floors',),
    ],
    normalizationContext: ['groups' => ['floor:read']]
)]
#[ORM\Entity(repositoryClass: FloorRepository::class)]
class Floor
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['floor:read', 'tenant:read', 'terminal:read', 'infrastructure:read', 'category:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['floor:read', 'tenant:read'])]
    private ?string $mapImage = null;

    #[Vich\UploadableField(mapping: 'floor_images', fileNameProperty: 'mapImage')]
    #[Assert\Image(mimeTypes: ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'])]
    private ?File $mapImageFile = null;

    #[ORM\OneToMany(targetEntity: Tenant::class, mappedBy: 'floor', cascade: ['all'])]
    #[Groups(['floor:read'])]
    private Collection $tenants;

    /**
     * @var Collection<int, Point>
     */
    #[ORM\OneToMany(targetEntity: Point::class, mappedBy: 'floor', cascade: ['all'])]
    private Collection $points;

    /**
     * @var Collection<int, Area>
     */
    #[ORM\OneToMany(targetEntity: Area::class, mappedBy: 'floor', cascade: ['all'])]
    private Collection $areas;

    #[ORM\OneToMany(targetEntity: Terminal::class, mappedBy: 'floor')]
    private Collection $terminal;

    #[ORM\Column]
    #[Groups(['floor:read'])]
    private ?float $zoomStart = null;

    #[Assert\Callback]
    public function validateZoom(ExecutionContextInterface $context)
    {
        if ($this->zoomStart > 10 || $this->zoomStart < 1 ) {
            $context->buildViolation('Значение масштаба не может быть меньше 1 и больше 10')
                ->atPath('zoomStart')
                ->addViolation();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMapImage(): ?string
    {
        return $this->mapImage;
    }

    public function setMapImage(?string $mapImage): static
    {
        $this->mapImage = $mapImage;

        return $this;
    }

    public function getMapImageFile(): ?File
    {
        return $this->mapImageFile;
    }

    public function setMapImageFile(?File $mapImageFile): self
    {
        $this->mapImageFile = $mapImageFile;
        if (null !== $mapImageFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return Collection<int, Point>
     */
    public function getPoints(): Collection
    {
        return $this->points;
    }

    public function addPoint(Point $point): static
    {
        if (!$this->points->contains($point)) {
            $this->points->add($point);
            $point->setFloor($this);
        }

        return $this;
    }

    public function removePoint(Point $point): static
    {
        if ($this->points->removeElement($point)) {
            // set the owning side to null (unless already changed)
            if ($point->getFloor() === $this) {
                $point->setFloor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Area>
     */
    public function getAreas(): Collection
    {
        return $this->areas;
    }

    public function addArea(Area $area): static
    {
        if (!$this->areas->contains($area)) {
            $this->areas->add($area);
            $area->setFloor($this);
        }

        return $this;
    }

    public function removeArea(Area $area): static
    {
        if ($this->areas->removeElement($area)) {
            // set the owning side to null (unless already changed)
            if ($area->getFloor() === $this) {
                $area->setFloor(null);
            }
        }

        return $this;
    }

    public function getZoomStart(): float
    {
        return $this->zoomStart;
    }

    public function setZoomStart(float $zoomMin): static
    {
        $this->zoomStart = $zoomMin;

        return $this;
    }
}

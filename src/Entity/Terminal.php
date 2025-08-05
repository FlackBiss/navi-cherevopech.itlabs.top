<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\OpenApi\Model\Operation;
use App\Controller\Terminal\TerminalUpdateController;
use App\Dto\TerminalDto;
use App\Repository\TerminalRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(),
        new Patch(
            controller: TerminalUpdateController::class,
            input: TerminalDto::class
        )
    ],
    normalizationContext: ['groups' => ['terminal:read']],
    paginationEnabled: false,
)]
#[ORM\Entity(repositoryClass: TerminalRepository::class)]
class Terminal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['terminal:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['terminal:read'])]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'terminals')]
    #[ORM\JoinColumn(onDelete:"SET NULL")]
    private ?Node $node = null;

    #[ORM\ManyToOne(inversedBy: 'terminals')]
    #[ORM\JoinColumn(onDelete:"SET NULL")]
    private ?Area $area = null;

    public function __toString(): string
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getNode(): ?Node
    {
        return $this->node;
    }

    public function setNode(?Node $node): static
    {
        $this->node = $node;

        return $this;
    }

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function setArea(?Area $area): static
    {
        $this->area = $area;

        return $this;
    }

    #[Groups(['terminal:read'])]
    public function getPoints(): ?Point
    {
        return $this->getNode()?->getPoint();
    }
}

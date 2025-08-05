<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\Controller\Node\NodeCreateController;
use App\Controller\Node\NodeUpdateController;
use App\DataProvider\NodeNavigateDataProvider;
use App\Dto\NodeDto;
use App\Repository\NodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\MaxDepth;

#[ApiResource(
    operations: [
        new Get(
            'navigate',
            openapi: new Operation(
                parameters: [
                    [
                        'name' => 'from',
                        'in' => 'query',
                        'required' => true,
                        'type' => 'integer'
                    ],
                    [
                        'name' => 'to',
                        'in' => 'query',
                        'required' => true,
                        'type' => 'integer'
                    ],
                ]
            ),
            provider: NodeNavigateDataProvider::class
        ),
        new GetCollection(),
        new Post(
            controller: NodeCreateController::class,
            input: NodeDto::class
        ),
        new Patch(
            controller: NodeUpdateController::class,
            input: NodeDto::class
        ),
        new Delete(),
        new Get(),
    ],
    normalizationContext: ['groups' => ['node:read']],
    paginationEnabled: false
)]
#[ORM\Entity(repositoryClass: NodeRepository::class)]
class Node
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['node:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['all'], inversedBy: 'nodes')]
    #[Groups(['node:read'])]
    private ?Point $point = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'nodes')]
    #[ORM\JoinTable(name: 'node_relations')]
    #[ORM\JoinColumn(name: 'parent_node_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'child_node_id', referencedColumnName: 'id')]
    private Collection $nodes;

    #[ORM\OneToMany(targetEntity: Terminal::class, mappedBy: 'node')]
    #[ORM\JoinColumn(onDelete:"SET NULL")]
    private Collection $terminals;

    /**
     * @var Collection<int, Tenant>
     */
    #[ORM\OneToMany(targetEntity: Tenant::class, mappedBy: 'node')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private Collection $tenants;

    public function __construct()
    {
        $this->nodes = new ArrayCollection();
        $this->terminals = new ArrayCollection();
        $this->tenants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoint(): ?Point
    {
        return $this->point;
    }

    public function setPoint(?Point $point): static
    {
        $this->point = $point;

        return $this;
    }

    /**
     * @param Collection $nodes
     */
    public function setNodes(Collection $nodes): void
    {
        $this->nodes = $nodes;
    }

    /**
     * @return Collection<int, self>
     */
    public function getNodes(): Collection
    {
        return $this->nodes;
    }

    public function addNode(self $node): static
    {
        if (!$this->nodes->contains($node)) {
            $this->nodes->add($node);
        }

        return $this;
    }

    public function removeNode(self $node): static
    {
        $this->nodes->removeElement($node);

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
            $terminal->setNode($this);
        }

        return $this;
    }

    public function removeTerminal(Terminal $terminal): static
    {
        if ($this->terminals->removeElement($terminal)) {
            // set the owning side to null (unless already changed)
            if ($terminal->getNode() === $this) {
                $terminal->setNode(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tenant>
     */
    public function getTenants(): Collection
    {
        return $this->tenants;
    }

    public function addTenant(Tenant $tenant): static
    {
        if (!$this->tenants->contains($tenant)) {
            $this->tenants->add($tenant);
            $tenant->setNode($this);
        }

        return $this;
    }

    public function removeTenant(Tenant $tenant): static
    {
        if ($this->tenants->removeElement($tenant)) {
            // set the owning side to null (unless already changed)
            if ($tenant->getNode() === $this) {
                $tenant->setNode(null);
            }
        }

        return $this;
    }
}

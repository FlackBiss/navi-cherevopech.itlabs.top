<?php

namespace App\Entity;

use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use App\Controller\QueriesController;
use App\Entity\Traits\CreatedAtTrait;
use App\Repository\QueriesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: QueriesRepository::class)]
#[Post(
    controller: QueriesController::class,
    openapi: new Operation(
        requestBody: new RequestBody(
            content: new \ArrayObject([
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'tenantId' => [
                                'type' => 'string',
                            ],
                            'type' => [
                                'type' => 'string',
                            ],
                        ],
                    ],
                ],
            ])
        )
    ),
)]
class Queries
{
    use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'queries')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Tenant $tenant = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function setTenant(?Tenant $tenant): static
    {
        $this->tenant = $tenant;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }
}

<?php

namespace App\DataProvider;


use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Node;
use App\Repository\NodeRepository;
use App\Service\AStarService;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class NodeNavigateDataProvider implements ProviderInterface
{
    public function __construct(
        private NodeRepository $nodeRepository,
    )
    {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Node::class === $resourceClass;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!key_exists('from', $context['filters']) or !key_exists('to', $context['filters'])) {
            throw new BadRequestException('from and to required');
        }

        $from = $this->nodeRepository->find($context['filters']['from']);
        $to = $this->nodeRepository->find($context['filters']['to']);

        if (!$from or !$to) {
            throw new NotFoundHttpException('nodes not found');
        }

        return (new AStarService())->find_path($from, $to, $this->nodeRepository->findAll());
    }
}
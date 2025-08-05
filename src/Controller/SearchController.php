<?php

namespace App\Controller;

use App\Service\MeilisearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class SearchController extends AbstractController
{

    public function __construct(private readonly MeilisearchService $meilisearchService)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');
        $categoryIds = $request->query->all('categoryIds');

        $categoryIds = array_map('intval', $categoryIds);

        $results = $this->meilisearchService->search($query, $categoryIds);

        return new JsonResponse($results);
    }
}
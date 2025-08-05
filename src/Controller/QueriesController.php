<?php

namespace App\Controller;

use App\Entity\Queries;
use App\Repository\QueriesRepository;
use App\Repository\TenantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class QueriesController extends AbstractController
{
    public function __construct(
        private readonly QueriesRepository $queriesRepository,
        private readonly TenantRepository $tenantRepository,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $tenantId = $request->request->get('tenantId');
        $type = $request->request->get('type');

        $tenant = $this->tenantRepository->find($tenantId);

        if (!$tenantId || !$type || !$tenant) {
            throw new NotFoundHttpException('Invalid tenant or type');
        }

        $query = new Queries();
        $query->setTenant($tenant);
        $query->setType($type);

        $this->queriesRepository->save($query, true);

        return $this->json(['success' => true,]);
    }
}
<?php

namespace App\Controller\Tenant;

use App\Dto\TenantDto;
use App\Repository\AreaRepository;
use App\Repository\NodeRepository;
use App\Repository\TenantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class TenantUpdateController extends AbstractController
{
    public function __construct(
        private readonly TenantRepository $tenantRepository,
        private readonly NodeRepository $nodeRepository,
        private readonly AreaRepository $areaRepository,
    )
    {
    }

    public function __invoke(Request $request, SerializerInterface  $serializer): JsonResponse
    {
        $id = $request->attributes->all()['id'];

        $body = $serializer->deserialize($request->getContent(), TenantDto::class, 'json');

        $mapObject = $this->tenantRepository->find($id);

        if (!$mapObject) {
            throw new NotFoundHttpException("map object with id $id not found");
        }

        if ($body->getNode() !== null) {
            $nodeId = $body->getNode();
            $node = $this->nodeRepository->find($nodeId);
            if (!$node) {
                throw new NotFoundHttpException("node with id $nodeId not found");
            }
            $mapObject->setNode($node);
        }
        else
        {
            $mapObject->setNode($body->getNode());
        }

        if ($body->getArea() !== null) {
            $areaId = $body->getArea();
            $area = $this->areaRepository->find($areaId);
            if (!$area) {
                throw new NotFoundHttpException("node with id $areaId not found");
            }
            $mapObject->setArea($area);
        }
        else
        {
            $mapObject->setArea($body->getArea());
        }

        $this->tenantRepository->save($mapObject, true);

        return $this->json($mapObject, 201, [], ['groups' => 'tenant:read']);
    }
}
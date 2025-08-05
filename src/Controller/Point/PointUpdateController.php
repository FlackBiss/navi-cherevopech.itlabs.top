<?php

namespace App\Controller\Point;

use App\Dto\PointDto;
use App\Entity\Point;
use App\Repository\FloorRepository;
use App\Repository\PointRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class PointUpdateController extends AbstractController
{
    public function __construct(
        private readonly PointRepository $pointRepository,
        private readonly FloorRepository $floorRepository
    )
    {
    }

    public function __invoke(Request $request, SerializerInterface  $serializer): JsonResponse
    {
        $id = $request->attributes->all()['id'];

        $body = $serializer->deserialize($request->getContent(), PointDto::class, 'json');

        $point = $this->pointRepository->find($id);

        if (!$point) {
            throw new NotFoundHttpException("point with id $id not found");
        }

        if ($body->getFloor()) {
            $floor = $this->floorRepository->find($body->floor);

            if (!$floor) {
                throw new NotFoundHttpException("floor with id $body->floor not found");
            }
        }

        if ($body->getX()) {
            $point->setX($body->x);
        }

        if ($body->getY()) {
            $point->setY($body->y);
        }

        $this->pointRepository->save($point, true);

        return $this->json($point, 201, [], ['groups' => 'point:read']);
    }
}
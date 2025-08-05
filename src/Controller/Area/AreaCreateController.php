<?php

namespace App\Controller\Area;

use App\Dto\AreaDto;
use App\Entity\Area;
use App\Entity\Point;
use App\Repository\AreaRepository;
use App\Repository\FloorRepository;
use App\Repository\PointRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
class AreaCreateController extends AbstractController
{
    public function __construct(
        private readonly FloorRepository $floorRepository,
        private readonly AreaRepository $areaRepository,
    )
    {
    }

    public function __invoke(Request $request, SerializerInterface  $serializer, ValidatorInterface $validator): JsonResponse
    {
        $body = $serializer->deserialize($request->getContent(), AreaDto::class, 'json');
        $errors = $validator->validate($body);
        if (count($errors) > 0) {
            throw new BadRequestHttpException((string) $errors);
        }

        $area = new Area();

        $floor = $this->floorRepository->find($body->getFloor());

        if (!$floor) {
            $floor_id= $body->getFloor();
            throw new NotFoundHttpException("floor with id $floor_id not found");
        }

        $area->setFloor($floor);

        foreach ($body->getPoints() as $point_array) {
            $floor = $this->floorRepository->find($point_array['floor']);
            $point = new Point();
            $point->setX($point_array['x']);
            $point->setY($point_array['y']);
            $point->setFloor($floor);
            $area->addPoint($point);
        }

        $this->areaRepository->save($area, true);

        return $this->json($area, 201, [], ['groups' => 'area:read']);
    }
}
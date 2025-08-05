<?php

namespace App\Controller\Area;

use App\Dto\AreaDto;
use App\Entity\Point;
use App\Repository\AreaRepository;
use App\Repository\FloorRepository;
use App\Repository\PointRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class AreaUpdateController extends AbstractController
{
    public function __construct(
        private readonly PointRepository $pointRepository,
        private readonly FloorRepository $floorRepository,
        private readonly AreaRepository $areaRepository,
    )
    {
    }

    public function __invoke(Request $request, SerializerInterface  $serializer): JsonResponse
    {
        $id = $request->attributes->all()['id'];

        $body = $serializer->deserialize($request->getContent(), AreaDto::class, 'json');

        $area = $this->areaRepository->find($id);

        if (!$area) {
            throw new NotFoundHttpException("area with id $id not found");
        }

        if ($body->getFloor() !== null) {
            $floor = $this->floorRepository->find($body->getFloor());

            if (!$floor) {
                $floor_id= $body->getFloor();
                throw new NotFoundHttpException("floor with id $floor_id not found");
            }

            $area->setFloor($floor);
        }

        if ($body->getPoints() !== null) {
            $points = new ArrayCollection();
            foreach ($area->getPoints() as $point) {
                $this->pointRepository->remove($point, true);
            }
            foreach ($body->getPoints() as $point_array) {
                $floor = $this->floorRepository->find($point_array['floor']);
                $point = new Point();
                $point->setX($point_array['x']);
                $point->setY($point_array['y']);
                $point->setFloor($floor);
                $points->add($point);
            }
            $area->setPoints($points);
        }

        $this->areaRepository->save($area, true);

        return $this->json($area, 201, [], ['groups' => 'area:read']);
    }
}
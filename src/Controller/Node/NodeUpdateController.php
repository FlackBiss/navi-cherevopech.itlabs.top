<?php

namespace App\Controller\Node;

use App\Dto\NodeDto;
use App\Repository\FloorRepository;
use App\Repository\NodeRepository;
use App\Repository\PointRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class NodeUpdateController extends AbstractController
{
    public function __construct(
        private readonly PointRepository $pointRepository,
        private readonly FloorRepository $floorRepository,
        private readonly NodeRepository $nodeRepository,

    )
    {
    }

    public function __invoke(Request $request, SerializerInterface  $serializer): JsonResponse
    {
        $id = $request->attributes->all()['id'];

        $body = $serializer->deserialize($request->getContent(), NodeDto::class, 'json');

        $node = $this->nodeRepository->find($id);

        if (!$node) {
            throw new NotFoundHttpException("node with id $id not found");
        }

        if ($body->getPoint()) {
            $point = $this->pointRepository->find($node->getPoint()->getId());

            if ($body->point->getFloor() !== null) {
                $floor_id = $body->point->getFloor();
                $floor = $this->floorRepository->find($floor_id);

                if (!$floor) {
                    throw new NotFoundHttpException("floor with id $floor_id not found");
                }

                $point->setFloor($floor);
            }


            if ($body->point->getX() !== null) {
                $point->setX($body->point->x);
            }

            if ($body->point->getY() !== null) {
                $point->setY($body->point->y);
            }

            $node->setPoint($point);
        }

        if ($body->getNodes() !== null) {
            $nodes = new ArrayCollection();
            foreach ($body->nodes as $node_id) {
                $node_item = $this->nodeRepository->find($node_id);
                if (!$node_item) {
                    throw new BadRequestHttpException("node with id $node_id not found");
                }
                $nodes->add($node_item);
            }
            $node->setNodes($nodes);
        }

        $this->nodeRepository->save($node, true);

        return $this->json($node, 201, [], ['groups' => 'node:read']);
    }
}
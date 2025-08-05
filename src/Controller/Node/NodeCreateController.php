<?php

namespace App\Controller\Node;

use App\Dto\NodeDto;
use App\Entity\Node;
use App\Entity\Point;
use App\Repository\FloorRepository;
use App\Repository\NodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
class NodeCreateController extends AbstractController
{
    public function __construct(
        private readonly NodeRepository  $nodeRepository,
        private readonly FloorRepository $floorRepository,
    )
    {
    }

    public function __invoke(
        Request             $request,
        SerializerInterface $serializer,
        ValidatorInterface  $validator,
    ): JsonResponse
    {
        $body = $serializer->deserialize($request->getContent(), NodeDto::class, 'json');
        $errors = $validator->validate($body);
        if (count($errors) > 0) {
            throw new BadRequestHttpException((string)$errors);
        }
        $errors = $validator->validate($body->getPoint());
        if (count($errors) > 0) {
            throw new BadRequestHttpException((string)$errors);
        }

        $point = new Point();
        $point->setX($body->point->x);
        $point->setY($body->point->y);

        $floor = $this->floorRepository->find($body->point->floor);

        if (!$floor) {
            $floor_id = $body->point->floor;
            throw new NotFoundHttpException("floor with id $floor_id not found");
        }

        $point->setFloor($floor);

        $node = new Node();
        $node->setPoint($point);
        if ($body->nodes) {
            foreach ($body->nodes as $node_id) {
                $node_item = $this->nodeRepository->find($node_id);
                if (!$node_item) {
                    throw new BadRequestHttpException("node with id $node_id not found");
                }
                $node->addNode($node_item);
            }
        }

        $this->nodeRepository->save($node, true);

        return $this->json($node, 201, [], ['groups' => 'node:read']);
    }
}
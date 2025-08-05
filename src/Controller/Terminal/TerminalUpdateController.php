<?php

namespace App\Controller\Terminal;

use App\Dto\TerminalDto;
use App\Repository\AreaRepository;
use App\Repository\NodeRepository;
use App\Repository\TerminalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class TerminalUpdateController  extends AbstractController
{
    public function __construct(
        private readonly TerminalRepository $terminalRepository,
        private readonly NodeRepository $nodeRepository,
        private readonly AreaRepository $areaRepository,
    )
    {
    }

    public function __invoke(Request $request, SerializerInterface  $serializer): JsonResponse
    {
        $id = $request->attributes->all()['id'];

        $body = $serializer->deserialize($request->getContent(), TerminalDto::class, 'json');

        $terminal = $this->terminalRepository->find($id);

        if (!$terminal) {
            throw new NotFoundHttpException("map object with id $id not found");
        }

        if ($body->getNode() !== null) {
            $nodeId = $body->getNode();
            $node = $this->nodeRepository->find($nodeId);
            if (!$node) {
                throw new NotFoundHttpException("node with id $nodeId not found");
            }
            $terminal->setNode($node);
        }
        else
        {
            $terminal->setNode($body->getNode());
        }

        if ($body->getArea() !== null) {
            $areaId = $body->getArea();
            $area = $this->areaRepository->find($areaId);
            if (!$area) {
                throw new NotFoundHttpException("node with id $areaId not found");
            }
            $terminal->setArea($area);
        }
        else
        {
            $terminal->setArea($body->getArea());
        }

        $this->terminalRepository->save($terminal, true);

        return $this->json($terminal, 201, [], ['groups' => 'terminal:read']);
    }
}
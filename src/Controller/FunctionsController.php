<?php

namespace App\Controller;

use App\Entity\Functions;
use App\Repository\FunctionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class FunctionsController extends AbstractController
{
    public function __construct(
        private readonly FunctionsRepository $functionsRepository,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $title = $data['title'];

        if (!$title) {
            throw new NotFoundHttpException('Invalid title');
        }

        $function = $this->functionsRepository->findOneBy(['title' => $title]);

        if (!$function) {
            $function = new Functions();
            $function->setTitle($title);
        }

        $function->setCount($function->getCount() + 1);

        $this->functionsRepository->save($function, true);

        return $this->json(['success' => true,]);
    }
}
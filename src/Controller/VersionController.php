<?php

namespace App\Controller;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class VersionController extends ApiController
{
  #[Route('/versions', methods: ['GET'])]
  #[IsGranted('PUBLIC_ACCESS')]
  public function config(ParameterBagInterface $params): JsonResponse
  {
    return $this->json($params->get('app_version'));
  }
}

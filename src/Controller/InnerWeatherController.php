<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Controller\ApiController;
use App\Application\InnerWeather\ListInnerWeather;

final class InnerWeatherController extends ApiController
{
    #[Route('/inner-weathers', methods: ['GET'])]
    public function list(
        ListInnerWeather $listInnerWeatherService
    ): JsonResponse {
        $innerWeathers = $listInnerWeatherService->execute();
        return $this->respondItems($innerWeathers, JsonResponse::HTTP_OK);
    }
}

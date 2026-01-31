<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Controller\ApiController;
use App\Application\InnerWeatherResponse\CreateInnerWeatherResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Exception\MissingPayloadException;
use App\Application\InnerWeather\GetInnerWeather;
use App\Application\InnerWeatherResponse\ListInnerWeatherResponse;

final class InnerWeatherResponseController extends ApiController
{
    #[Route('/inner-weather-responses', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(
        Request $request,
        CreateInnerWeatherResponse $createInnerWeatherResponse,
        GetInnerWeather $getInnerWeather,
        UserInterface $author,
    ): JsonResponse {
        $data = $request->toArray();
        $innerWeatherId = $data['innerWeatherId'] ?? null;

        if (!ctype_digit((string)$innerWeatherId)) {
            throw new MissingPayloadException('innerWeatherId');
        }
        $innerWeatherId = (int) $innerWeatherId;
        $innerWeather = $getInnerWeather->execute($innerWeatherId);
        $innerWeatherResponse = $createInnerWeatherResponse->execute($innerWeather, $author);
        return $this->respondItem($innerWeatherResponse, JsonResponse::HTTP_CREATED);
    }

    #[Route('/inner-weather-responses', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(
        Request $request,
        ListInnerWeatherResponse $listInnerWeatherResponse,
        UserInterface $author,
    ): JsonResponse {
        $day = $request->query->get('day');
        $filters = [
            'author' => $author,
            'day' => $day,
        ];

        $innerWeatherResponses = $listInnerWeatherResponse->execute($filters);
        return $this->respondItems($innerWeatherResponses, JsonResponse::HTTP_OK);
    }
}

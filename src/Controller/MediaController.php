<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use App\Controller\ApiController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class MediaController extends ApiController
{
  public function __construct(
    private string $storageDir
  ) {}

  #[Route('/media/{path}', name: 'media_show', requirements: ['path' => '.+'], methods: ['GET'])]
  public function show(string $path): Response
  {
    $baseDir = rtrim($this->storageDir, '/');
    $filePath = realpath($baseDir . '/' . $path);
    // 🔒 Sécurité : empêche ../
    if (!$filePath || !str_starts_with($filePath, $baseDir)) {
      return new Response('File not found', 404);
    }

    if (!file_exists($filePath) || !is_file($filePath)) {
      return new Response('File not found', 404);
    }

    $response = new BinaryFileResponse($filePath);
    $response->setContentDisposition(
      ResponseHeaderBag::DISPOSITION_INLINE,
      basename($filePath)
    );

    // 🧠 Cache (important pour mobile)
    $response->setPublic();
    $response->setMaxAge(60 * 60 * 24 * 7); // 7 jours
    $response->headers->addCacheControlDirective('immutable');

    return $response;
  }
}

<?php

namespace App\Application\Ai;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class OpenAiClient
{
  public function __construct(
    private string $apiKey,
    private HttpClientInterface $httpClient,
    private LoggerInterface $logger
  ) {}

  public function generate(string $prompt): string
  {
    $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
      'headers' => [
        'Authorization' => 'Bearer ' . $this->apiKey,
        'Content-Type' => 'application/json',
      ],
      'json' => [
        'model' => 'gpt-4o-mini',
        'messages' => [
          ['role' => 'system', 'content' => 'You are SELEN inner intelligence.'],
          ['role' => 'user', 'content' => $prompt],
        ],
        'temperature' => 0.7,
      ],
    ]);
    if ($response->getStatusCode() !== 200) {
      $this->logger->error('Failed to generate AI response', [
        'status_code' => $response->getStatusCode(),
        'content' => $response->getContent(false),
      ]);
      return '';
    }
    $data = $response->toArray();

    return $data['choices'][0]['message']['content'] ?? '';
  }
}

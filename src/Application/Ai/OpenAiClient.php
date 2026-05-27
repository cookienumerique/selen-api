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

  public function generate(string $userPrompt, string $systemPrompt): string
  {
    $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
      'headers' => [
        'Authorization' => 'Bearer ' . $this->apiKey,
        'Content-Type' => 'application/json',
      ],
      'json' => [
        'model' => 'gpt-4o-mini',
        'messages' => [
          ['role' => 'system', 'content' => $systemPrompt],
          ['role' => 'user', 'content' => $userPrompt],
        ],
        'temperature' => 0.3,
        'max_tokens' => 120,
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

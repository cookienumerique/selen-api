<?php

namespace App\Application\Subscription\GoogleWebhook;

use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

class GooglePubSubDecoder
{
    public function __construct(
        private LoggerInterface $logger
    ) {}
    public function execute(Request $request): ?array
    {
        $body = json_decode($request->getContent(), true);
        $this->logger->info('Google Webhook decoded', [
            'body' => $body
        ]);
        return $body['content'] ?? null;
    }
}

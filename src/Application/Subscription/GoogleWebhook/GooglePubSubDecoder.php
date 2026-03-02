<?php

namespace App\Application\Subscription\GoogleWebhook;

use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

class GooglePubSubDecoder
{
    public function execute(Request $request, LoggerInterface $logger): ?array
    {
        $body = json_decode($request->getContent(), true);
        $logger->info('Google Webhook decoded', [
            'body' => $body
        ]);
        return $body['content'] ?? null;
    }
}

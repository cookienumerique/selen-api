<?php

namespace App\Application\Subscription\GoogleWebhook;

use Symfony\Component\HttpFoundation\Request;

class GooglePubSubDecoder
{
    public function execute(Request $request): ?array
    {
        $body = json_decode($request->getContent(), true);
        return $body['content'] ?? null;
    }
}

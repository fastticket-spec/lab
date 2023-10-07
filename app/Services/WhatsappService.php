<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappService {
    public function sendMessage($phone, $link, $name): void
    {
        $response = Http::withHeaders(
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'PublicId' => config('whatsapp.public_id'),
                'Secret' => config('whatsapp.secret')
            ]
        )->post('https://apis.unifonic.com/v1/messages',
            [
                "recipient" => [
                    "contact" => "+$phone",
                    "channel" => "whatsapp"
                ],
                "content" => [
                    "type" => "template",
                    "name" => "payment_due",
                    "language" => [
                        "code" => "en"
                    ],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $name
                                ],
                                [
                                    "type" => "text",
                                    "text" => $link
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        if ($response->failed()) {
            \Log::debug($response->body());
        }
    }
}

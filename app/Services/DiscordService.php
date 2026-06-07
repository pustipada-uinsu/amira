<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordService
{
    public static function send(string $message): void
    {
        $url = config('services.discord.webhook_url');

        if (empty($url)) {
            return;
        }

        try {
            Http::post($url, [
                'content' => $message
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim notifikasi Discord: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Library;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class LoggerTrade
{
    public static function sendBot(string $note) {

        $token = env('BOT_TELEGRAM_TOKEN');
        $chat_id = env('BOT_TELEGRAM_CHAT_IDS');

        $message = '<b>' . env('APP_NAME') . '</b>' . PHP_EOL
            . '<b>' . env('APP_ENV') . '</b>' . PHP_EOL
            . '<i>Message:</i>' . PHP_EOL
            . $note;

        try {
            $ids = explode(',', $chat_id);

            foreach ($ids as $id) {

                $telegram = new Api($token);
                $response = $telegram->sendMessage([
                    'chat_id' => $chat_id,
                    'text' => $message,
                    'parse_mode' => 'HTML'
                ]);

//                file_get_contents(
//                    'https://api.telegram.org/bot' . $token . '/sendMessage?'
//                    . http_build_query([
//                        'text' => $message,
//                        'chat_id' => $id,
//                        'parse_mode' => 'html'
//                    ])
//                );

            }
        } catch (\Exception $e) {
            Log::error('TelegramLog bad token/chat_id.');
        }

        Log::info($note);

    }

}

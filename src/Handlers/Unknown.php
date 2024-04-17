<?php

namespace PenisBot\Handlers;

use Telegram\Bot\Api as TelegramAPI;
use Telegram\Bot\Objects\Update;

class Unknown implements HandlerInterface
{
    public function process(TelegramAPI $telegram, Update $update): void
    {
        $telegram->sendMessage([
            'chat_id' => $update->getMessage()->getChat()->getId(),
            'text' => sprintf('Бля, хуй знает что делать. Напиши гигачаду @EvgKot. udtID: %s', $update->getUpdateId()),
            'reply_to_message_id' => $update->getMessage()->getMessageId(),
        ]);
    }
}

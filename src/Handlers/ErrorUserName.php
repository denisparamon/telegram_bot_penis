<?php

namespace PenisBot\Handlers;

use Telegram\Bot\Api as TelegramAPI;
use Telegram\Bot\Objects\Update;

class ErrorUserName implements HandlerInterface
{
    public function process(TelegramAPI $telegram, Update $update): void
    {
        $telegram->sendMessage([
            'chat_id' => $update->getMessage()->getChat()->getId(),
            'text' => 'Заполни себе username, чтобы я мог тебя тэгать как пидора, либо иди нахуй',
            'reply_to_message_id' => $update->getMessage()->getMessageId(),
        ]);
    }
}

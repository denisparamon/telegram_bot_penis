<?php

namespace PenisBot\Handlers;

use Telegram\Bot\Actions;
use Telegram\Bot\Api as TelegramAPI;
use Telegram\Bot\Objects\Update;

class PrivateChat implements HandlerInterface
{
    public function process(TelegramAPI $telegram, Update $update): void
    {
        $chatId = $update->getMessage()->getChat()->getId();

        $telegram->sendMessage([
            'chat_id' => $update->getMessage()->getChat()->getId(),
            'text' => 'Сорян бро в привате не общаюсь',
        ]);

        sleep(1);
        $telegram->sendChatAction([
            'chat_id' => $chatId,
            'action' => Actions::TYPING,
        ]);
        sleep(1);

        $telegram->sendMessage([
            'chat_id' => $update->getMessage()->getChat()->getId(),
            'text' => 'Пидр',
        ]);
    }
}

<?php

namespace PenisBot\Handlers;

use PenisBot\Bot;
use Telegram\Bot\Api as TelegramAPI;
use Telegram\Bot\Objects\Update;

class BotAddedToChat implements HandlerInterface
{
    private Bot $bot;

    public function __construct(Bot $bot)
    {
        $this->bot = $bot;
    }

    public function process(TelegramAPI $telegram, Update $update): void
    {
        $telegram->sendMessage([
            'chat_id' => $update->getMessage()->getChat()->getId(),
            'text' => 'Здарова! Я ваш новый папочка 😈 Жмякай на кнопку, если не ссылко: /reg@'
                . $this->bot->getName(),
        ]);
    }
}

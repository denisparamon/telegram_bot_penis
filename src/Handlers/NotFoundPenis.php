<?php

namespace PenisBot\Handlers;

use PenisBot\Bot;
use Telegram\Bot\Api as TelegramAPI;
use Telegram\Bot\Objects\Update;

class NotFoundPenis implements HandlerInterface
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
            'text' => sprintf(
                'Не хватает участников с хуями... Получите свои пиписки %s@%s',
                PenisCommand::COMMAND_NAME,
                $this->bot->getName()
            ),
        ]);
    }
}

<?php

namespace PenisBot\Handlers;

use Telegram\Bot\Api as TelegramAPI;
use Telegram\Bot\Objects\Update;

class Error implements HandlerInterface
{
    public function process(TelegramAPI $telegram, Update $update): void
    {
        $telegram->sendMessage([
            'chat_id' => $update->getMessage()->getChat()->getId(),
            'text' => sprintf('Какая-то поебень... Нассы себе на лицо и попробуй ещё раз или напиши этому гигачаду @EvgKot. udtId: %s', $update->getUpdateId()),
            'reply_to_message_id' => $update->getMessage()->getMessageId(),
        ]);
    }
}

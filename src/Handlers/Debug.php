<?php

namespace PenisBot\Handlers;

use Telegram\Bot\Api as TelegramAPI;
use Telegram\Bot\Objects\Update;

class Debug implements HandlerInterface
{
    public const COMMAND_NAME = '/debug';

    public function process(TelegramAPI $telegram, Update $update): void
    {
        $telegram->sendMessage([
            'chat_id' => $update->getMessage()->getChat()->getId(),
            'parse_mode' => 'MarkdownV2',
            'text' => sprintf('```%s```', print_r($update, true)),
            'reply_to_message_id' => $update->getMessage()->getMessageId(),
        ]);
    }
}

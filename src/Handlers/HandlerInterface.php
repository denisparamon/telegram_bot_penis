<?php

namespace PenisBot\Handlers;

use Telegram\Bot\Api as TelegramAPI;
use Telegram\Bot\Objects\Update;

interface HandlerInterface
{
    public function process(TelegramAPI $telegram, Update $update): void;
}

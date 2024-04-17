<?php

namespace PenisBot;

use Telegram\Bot\Api as TelegramAPI;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

class Negotiator extends TelegramAPI
{
    private Update $update;

    public function __construct($token = null, $async = false, $httpClientHandler = null)
    {
        parent::__construct($token, $async, $httpClientHandler);
        $this->update = $this->getWebhookUpdate();
    }

    public function sendMessage($params): Message
    {
        return parent::sendMessage($this->trySetThread($params));
    }

    public function sendChatAction(array $params): bool
    {
        return parent::sendChatAction($this->trySetThread($params));
    }

    private function trySetThread(array $params): array
    {
        if (!empty($params['reply_to_message_id'])) {
            return $params;
        }

        $params['message_thread_id'] = $this->update->getMessage()->get('is_topic_message', false) ? $this->update->getMessage()->get('message_thread_id') : null;

        return $params;
    }
}

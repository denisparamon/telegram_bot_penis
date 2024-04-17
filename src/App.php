<?php

namespace PenisBot;

use Telegram\Bot\Actions;
use Telegram\Bot\Api;

class App
{
    private Api $telegramApi;
    private UpdateHandlerResolver $updateHandlerResolver;
    private LoggerUpdate $loggerUpdate;

    public function __construct(
        Api $telegramApi,
        UpdateHandlerResolver $updateHandlerResolver,
        LoggerUpdate $loggerUpdate
    )
    {
        $this->telegramApi = $telegramApi;
        $this->updateHandlerResolver = $updateHandlerResolver;
        $this->loggerUpdate = $loggerUpdate;
    }

    public function run(): void
    {
        $update = $this->telegramApi->getWebhookUpdates();
        $rawUpdate = file_get_contents('php://input');

        $this->loggerUpdate->log($update, $rawUpdate);
        $handler = $this->updateHandlerResolver->resolve($update);

        if ($handler === null) {
            return;
        }

        if ($update->getMessage() !== null) {
            $this->telegramApi->sendChatAction([
                'chat_id' => $update->getMessage()->getChat()->getId(),
                'action' => Actions::TYPING,
            ]);
        }

        $handler->process($this->telegramApi, $update);
    }
}

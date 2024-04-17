<?php

namespace PenisBot\Handlers;

use PenisBot\Domain\Actions\Penis\GetTopPenisesAction;
use Telegram\Bot\Api as TelegramAPI;
use Telegram\Bot\Objects\Update;

class TopPenisCommand implements HandlerInterface
{
    public const COMMAND_NAME = '/top_penis';

    private GetTopPenisesAction $getTopPenises;
    private NotFoundPenis $notFoundPenis;

    public function __construct(GetTopPenisesAction $getTopPenises, NotFoundPenis $notFoundPenis)
    {
        $this->getTopPenises = $getTopPenises;
        $this->notFoundPenis = $notFoundPenis;
    }

    public function process(TelegramAPI $telegram, Update $update): void
    {
        $chatId = $update->getMessage()->getChat()->getId();

        $penises = $this->getTopPenises->getTopPenises($chatId);

        if (count($penises) === 0) {
            $this->notFoundPenis->process($telegram, $update);
            return;
        }

        $top = [];

        foreach ($penises as $idx => $penis) {
            $text = '%s. @%s у него писунька %s см 🤡';

            if ($idx === 0) {
                $text = '%s. @%s настоящий гигачад с елдой %s см 😱';
            }

            if ($idx === 1) {
                $text = '%s. @%s полупокер но с большим хреном %s см 💪';
            }

            if ($idx === 2) {
                $text = '%s. @%s лучше быть третьим чем выступать в цирке %s см 🐺';
            }

            if ($idx === (count($penises) - 1)) {
                $text = '%s. @%s куколд с %s см 🤡';
            }

            $top[] = sprintf($text, $idx + 1, $penis->getUsername(), $penis->getSize());
        }

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'disable_notification' => true,
            'text' => implode(PHP_EOL, $top),
        ]);
    }
}

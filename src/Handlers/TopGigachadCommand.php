<?php

namespace PenisBot\Handlers;

use PenisBot\Domain\Actions\Gigachad\GetTopGigachadsAction;
use Telegram\Bot\Api as TelegramAPI;
use Telegram\Bot\Objects\Update;

class TopGigachadCommand implements HandlerInterface
{
    public const COMMAND_NAME = '/top_gigachad';

    private GetTopGigachadsAction $getTopGigachadsAction;
    private NotFoundGigachads $notFoundGigachads;

    public function __construct(GetTopGigachadsAction $getTopGigachadsAction, NotFoundGigachads $notFoundGigachads)
    {
        $this->getTopGigachadsAction = $getTopGigachadsAction;
        $this->notFoundGigachads = $notFoundGigachads;
    }

    public function process(TelegramAPI $telegram, Update $update): void
    {
        $chatId = $update->getMessage()->getChat()->getId();

        $pidors = $this->getTopGigachadsAction->getTopGigachads($chatId);

        if (count($pidors) === 0) {
            $this->notFoundGigachads->process($telegram, $update);
            return;
        }

        $top = [];

        foreach ($pidors as $idx => $pidor) {
            $text = '%s. @%s - %s раз(а). Четкий пацан 🐺';

            if ($idx === 0) {
                $text = '%s. @%s - %s раз(а). Альфа самец 💪😎';
            }

            if ($idx === (count($pidors) - 1)) {
                $text = '%s. @%s - %s раз(а). Похож на пидора 🤡';
            }

            $top[] = sprintf($text, $idx + 1, $pidor->getUsername(), $pidor->getCount());
        }

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'disable_notification' => true,
            'text' => implode(PHP_EOL, $top),
        ]);
    }
}

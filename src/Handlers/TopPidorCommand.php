<?php

namespace PenisBot\Handlers;

use PenisBot\Domain\Actions\Pidor\GetTopPidorsAction;
use Telegram\Bot\Api as TelegramAPI;
use Telegram\Bot\Objects\Update;

class TopPidorCommand implements HandlerInterface
{
    public const COMMAND_NAME = '/top_pidor';

    private GetTopPidorsAction $getTopPidorsAction;
    private NotFoundPidors $notFoundPidors;

    public function __construct(GetTopPidorsAction $getTopPidorsAction, NotFoundPidors $notFoundPidors)
    {
        $this->getTopPidorsAction = $getTopPidorsAction;
        $this->notFoundPidors = $notFoundPidors;
    }

    public function process(TelegramAPI $telegram, Update $update): void
    {
        $chatId = $update->getMessage()->getChat()->getId();

        $pidors = $this->getTopPidorsAction->getTopPidors($chatId);

        if (count($pidors) === 0) {
            $this->notFoundPidors->process($telegram, $update);
            return;
        }

        $top = [];

        foreach ($pidors as $idx => $pidor) {
            $text = '%s. @%s - %s раз(а). Около пидорства 💩';

            if ($idx === 0) {
                $text = '%s. @%s - %s раз(а). Самый крепкий анус на деревне 🐓';
            }

            if ($idx === (count($pidors) - 1)) {
                $text = '%s. @%s - %s раз(а). Может даже он натурал 🤡';
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

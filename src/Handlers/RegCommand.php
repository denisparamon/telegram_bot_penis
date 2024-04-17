<?php

namespace PenisBot\Handlers;

use PenisBot\Bot;
use PenisBot\Domain\Actions\Member\GetMemberAction;
use PenisBot\Domain\Actions\Member\GetMembersAction;
use PenisBot\Domain\Actions\Member\RegisterMemberAction;
use Telegram\Bot\Api as TelegramAPI;
use Telegram\Bot\Objects\Update;

class RegCommand implements HandlerInterface
{
    public const COMMAND_NAME = '/reg';

    private GetMemberAction $getMember;
    private RegisterMemberAction $registerMember;
    private Bot $bot;
    private GetMembersAction $getMembersAction;

    public function __construct(
        GetMemberAction $getMember,
        RegisterMemberAction $registerMember,
        Bot $bot,
        GetMembersAction $getMembersAction
    ) {
        $this->getMember = $getMember;
        $this->registerMember = $registerMember;
        $this->bot = $bot;
        $this->getMembersAction = $getMembersAction;
    }

    public function process(TelegramAPI $telegram, Update $update): void
    {
        $userId = $update->getMessage()->getFrom()->getId();
        $chatId = $update->getMessage()->getChat()->getId();

        $member = $this->getMember->getMember($userId, $chatId);

        if ($member !== null) {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Чё надо? Однохуйственно ты уже в зареган...',
                'reply_to_message_id' => $update->getMessage()->getMessageId(),
            ]);

            return;
        }

        $this->registerMember->registerMember($userId, $chatId, $update->getMessage()->getFrom()->getUsername());

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => sprintf(
                'Велком ту зе клаб, бади 😎🤝😎. Теперь получай свою елдень %s@%s',
                PenisCommand::COMMAND_NAME,
                $this->bot->getName()
            ),
            'reply_to_message_id' => $update->getMessage()->getMessageId(),
        ]);

        $members = $this->getMembersAction->getMembersWithPenisForSpin($chatId);

        if (count($members) > 1) {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => sprintf(
                    'Теперь можете покрутить пидора дня %s@%s, а также альфа самца дня %s@%s',
                    PidorCommand::COMMAND_NAME,
                    $this->bot->getName(),
                    GigachadCommand::COMMAND_NAME,
                    $this->bot->getName()
                ),
                'reply_to_message_id' => $update->getMessage()->getMessageId(),
            ]);
        }
    }
}

<?php

namespace PenisBot;

use PenisBot\Handlers\BotAddedToChat;
use PenisBot\Handlers\Debug;
use PenisBot\Handlers\Error;
use PenisBot\Handlers\ErrorUserName;
use PenisBot\Handlers\GigachadCommand;
use PenisBot\Handlers\HandlerInterface;
use PenisBot\Handlers\PenisCommand;
use PenisBot\Handlers\PidorCommand;
use PenisBot\Handlers\PrivateChat;
use PenisBot\Handlers\RegCommand;
use PenisBot\Handlers\TopGigachadCommand;
use PenisBot\Handlers\TopPenisCommand;
use PenisBot\Handlers\TopPidorCommand;
use PenisBot\Handlers\Unknown;
use Telegram\Bot\Objects\Chat;
use Telegram\Bot\Objects\Update;

class UpdateHandlerResolver
{
    private Bot $bot;
    private Unknown $unknown;
    private PrivateChat $privateChat;
    private BotAddedToChat $botAddedToChat;
    private RegCommand $regCommand;
    private PenisCommand $penisCommand;
    private TopPenisCommand $topPenisCommand;
    private PidorCommand $pidorCommand;
    private TopPidorCommand $topPidorCommand;
    private GigachadCommand $gigachadCommand;
    private TopGigachadCommand $topGigachadCommand;
    private ErrorUserName $errorUserName;
    private Debug $debug;

    public function __construct(
        Bot $bot,
        Unknown $unknown,
        PrivateChat $privateChat,
        BotAddedToChat $botAddedToChat,
        RegCommand $regCommand,
        PenisCommand $penisCommand,
        TopPenisCommand $topPenisCommand,
        PidorCommand $pidorCommand,
        TopPidorCommand $topPidorCommand,
        GigachadCommand $gigachadCommand,
        TopGigachadCommand $topGigachadCommand,
        ErrorUserName $errorUserName,
        Debug $debug
    )
    {
        $this->bot = $bot;
        $this->unknown = $unknown;
        $this->privateChat = $privateChat;
        $this->botAddedToChat = $botAddedToChat;
        $this->regCommand = $regCommand;
        $this->penisCommand = $penisCommand;
        $this->topPenisCommand = $topPenisCommand;
        $this->pidorCommand = $pidorCommand;
        $this->topPidorCommand = $topPidorCommand;
        $this->gigachadCommand = $gigachadCommand;
        $this->topGigachadCommand = $topGigachadCommand;
        $this->errorUserName = $errorUserName;
        $this->debug = $debug;
    }

    public function resolve(Update $update): ?HandlerInterface
    {
        if ($this->isEmptyMessage($update)) {
            return null; // Пропускаем событие т.к. дальше обрабатываются только сообщения
        }

        if ($this->isPrivateChat($update)) {
            return $this->privateChat;
        }

        if ($this->isAddBotToChat($update)) {
            return $this->botAddedToChat;
        }

        if ($this->isRegCommand($update)) {
            if ($this->isEmptyUsername($update)) {
                return $this->errorUserName;
            }

            return $this->regCommand;
        }

        if ($this->isPenisCommand($update)) {
            return $this->penisCommand;
        }

        if ($this->isTopPenisCommand($update)) {
            return $this->topPenisCommand;
        }

        if ($this->isPidorCommand($update)) {
            return $this->pidorCommand;
        }

        if ($this->isTopPidorCommand($update)) {
            return $this->topPidorCommand;
        }

        if ($this->isGigachadCommand($update)) {
            return $this->gigachadCommand;
        }

        if ($this->isTopGigachadCommand($update)) {
            return $this->topGigachadCommand;
        }

        if ($this->isDebugCommand($update)) {
            return $this->debug;
        }

        if ($this->isReplyToMessage($update)) {
            return null;
        }

        return null;
        // слишком много ругается на разные, типа вышел чел из чата, переименовали чат и т.п.
        // пока оставлю так
        //return $this->unknown;
    }

    private function isEmptyMessage(Update $update): bool
    {
        return $update->getMessage() === null;
    }

    private function isPrivateChat(Update $update): bool
    {
        return $update->getMessage()->getChat()->getType() === 'private';
    }

    private function isGroup(Chat $chat): bool
    {
        return in_array($chat->getType(), [
            'group',
            'supergroup',
        ], true);
    }

    private function isAddBotToChat(Update $update): bool
    {
        return $this->isGroup($update->getMessage()->getChat()) &&
            $update->getMessage()->getNewChatParticipant() !== null &&
            $update->getMessage()->getNewChatParticipant()->getUsername() === $this->bot->getName();
    }

    private function isEmptyUsername(Update $update): bool
    {
        return $update->getMessage()->getFrom() === null ||
            empty($update->getMessage()->getFrom()->getUsername());
    }

    private function isRegCommand(Update $update): bool
    {
        return $this->isGroup($update->getMessage()->getChat()) &&
            str_starts_with($update->getMessage()->getText(), RegCommand::COMMAND_NAME);
    }

    private function isPenisCommand(Update $update): bool
    {
        return $this->isGroup($update->getMessage()->getChat()) &&
            str_starts_with($update->getMessage()->getText(), PenisCommand::COMMAND_NAME);
    }

    private function isTopPenisCommand(Update $update): bool
    {
        return $this->isGroup($update->getMessage()->getChat()) &&
            str_starts_with($update->getMessage()->getText(), TopPenisCommand::COMMAND_NAME);
    }

    private function isPidorCommand(Update $update): bool
    {
        return $this->isGroup($update->getMessage()->getChat()) &&
            str_starts_with($update->getMessage()->getText(), PidorCommand::COMMAND_NAME);
    }

    private function isTopPidorCommand(Update $update): bool
    {
        return $this->isGroup($update->getMessage()->getChat()) &&
            str_starts_with($update->getMessage()->getText(), TopPidorCommand::COMMAND_NAME);
    }

    private function isGigachadCommand(Update $update): bool
    {
        return $this->isGroup($update->getMessage()->getChat()) &&
            str_starts_with($update->getMessage()->getText(), GigachadCommand::COMMAND_NAME);
    }

    private function isTopGigachadCommand(Update $update): bool
    {
        return $this->isGroup($update->getMessage()->getChat()) &&
            str_starts_with($update->getMessage()->getText(), TopGigachadCommand::COMMAND_NAME);
    }

    private function isReplyToMessage(Update $update): bool
    {
        return $this->isGroup($update->getMessage()->getChat()) &&
            $update->getMessage()->getReplyToMessage() !== null;
    }

    private function isDebugCommand(Update $update): bool
    {
        return $this->isGroup($update->getMessage()->getChat()) &&
            str_starts_with($update->getMessage()->getText(), Debug::COMMAND_NAME);
    }
}

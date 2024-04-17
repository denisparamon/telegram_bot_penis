<?php

namespace PenisBot\Handlers;

use PenisBot\Bot;
use PenisBot\Domain\Actions\Member\GetMemberAction;
use PenisBot\Domain\Actions\Penis\GetPenisAction;
use PenisBot\Domain\Actions\Penis\NeedSkipSpinPenisSizeAction;
use PenisBot\Domain\Actions\Penis\RegisterPenisAction;
use PenisBot\Domain\Actions\Penis\SpinPenisSizeAction;
use PenisBot\Domain\Actions\Penis\UpdatePenisSizeAction;
use PenisBot\Domain\Elements\Penis;
use PenisBot\Domain\Elements\SpinPenisResult;
use PenisBot\Locker;
use Telegram\Bot\Actions;
use Telegram\Bot\Api as TelegramAPI;
use Telegram\Bot\Objects\Update;

class PenisCommand implements HandlerInterface
{
    public const COMMAND_NAME = '/penis';

    private Bot $bot;
    private GetMemberAction $getMember;
    private GetPenisAction $getPenis;
    private RegisterPenisAction $registerPenis;
    private NeedSkipSpinPenisSizeAction $canSpinPenisSize;
    private SpinPenisSizeAction $spinPenisSize;
    private UpdatePenisSizeAction $updatePenisSize;
    private Locker $locker;

    public function __construct(
        Bot $bot,
        GetMemberAction $getMember,
        GetPenisAction $getPenis,
        RegisterPenisAction $registerPenis,
        NeedSkipSpinPenisSizeAction $canSpinPenisSize,
        SpinPenisSizeAction $spinPenisSize,
        UpdatePenisSizeAction $updatePenisSize,
        Locker $locker
    ) {
        $this->bot = $bot;
        $this->getMember = $getMember;
        $this->getPenis = $getPenis;
        $this->registerPenis = $registerPenis;
        $this->canSpinPenisSize = $canSpinPenisSize;
        $this->spinPenisSize = $spinPenisSize;
        $this->updatePenisSize = $updatePenisSize;
        $this->locker = $locker;
    }

    public function process(TelegramAPI $telegram, Update $update): void
    {
        $userId = $update->getMessage()->getFrom()->getId();
        $chatId = $update->getMessage()->getChat()->getId();
        $messageId = $update->getMessage()->getMessageId();
        $lockKey = 'penis' . $chatId . $userId;

        if ($this->locker->issetLock($lockKey)) {
            return;
        }

        $this->locker->lock($lockKey);

        $member = $this->getMember->getMember($userId, $chatId);

        if ($member === null) {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => sprintf(
                    'Эээ блэд. Зарагейстреруся потом приходи. %s@%s',
                    RegCommand::COMMAND_NAME,
                    $this->bot->getName()
                ),
                'reply_to_message_id' => $messageId,
            ]);
        }

        $penis = $this->getPenis->getPenisByMember($member);

        if ($penis === null) {
            $penis = $this->registerPenis->registerPenis($member);

            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => sprintf(
                    'Поздравляю с новой писюнькой!🥳 У тебя стартовые %s см. Можешь теперь поиграть в ебаное казино со своим писюном %s@%s',
                    $penis->getSize(),
                    static::COMMAND_NAME,
                    $this->bot->getName()
                ),
                'reply_to_message_id' => $messageId,
            ]);

            return;
        }

        if ($this->canSpinPenisSize->needSkipSpinPenisSize($penis)) {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Могу только по губам поводить. Приходи позже...',
                'reply_to_message_id' => $messageId,
            ]);

            return;
        }

        $spinResult = $this->spinPenisSize->spinPenisSize($penis);
        $penis = $this->updatePenisSize->updatePenisSize($penis, $spinResult, true);

        if ($spinResult->isAdd()) {
            $this->sendMessageAdd($telegram, $penis, $spinResult, $chatId, $messageId);
        }

        if ($spinResult->isDiff()) {
            $this->sendMessageDiff($telegram, $penis, $spinResult, $chatId, $messageId);
        }

        if ($spinResult->isZero()) {
            $this->sendMessageZero($telegram, $penis, $chatId, $messageId);
        }

        if ($spinResult->isReset()) {
            $this->sendMessageReset($telegram, $penis, $chatId, $messageId);
        }

        $this->locker->unlock($lockKey);
    }

    private function sendMessageAdd(
        TelegramAPI $telegram,
        Penis $penis,
        SpinPenisResult $spinResult,
        int $chatId,
        int $messageId
    ): void {
        switch ($spinResult->getDiffSize()) {
            case 1:
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => sprintf(
                        '+1 и все. Твой сайз: %s см',
                        $penis->getSize()
                    ),
                    'reply_to_message_id' => $messageId,
                ]);
                return;
            case 2:
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => sprintf(
                        '+2 это уже лучше чем +1 🤡 Твой сайз: %s см',
                        $penis->getSize()
                    ),
                    'reply_to_message_id' => $messageId,
                ]);
                return;
            case 3:
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => sprintf(
                        '+3 на повышение идешь?🍆 Твой сайз: %s см',
                        $penis->getSize()
                    ),
                    'reply_to_message_id' => $messageId,
                ]);
                return;
            case 4:
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => sprintf(
                        '+4 воу чел! Я смотрю ты подходишь к делу серьезно 😎 Твой сайз: %s см',
                        $penis->getSize()
                    ),
                    'reply_to_message_id' => $messageId,
                ]);
                return;
            case 5:
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Что блять?',
                ]);

                $telegram->sendChatAction([
                    'chat_id' => $chatId,
                    'action' => Actions::TYPING,
                ]);
                sleep(1);

                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Не может быть...',
                ]);

                $telegram->sendChatAction([
                    'chat_id' => $chatId,
                    'action' => Actions::TYPING,
                ]);
                sleep(1);

                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => sprintf(
                        'Это RAMPAGE🔥 +5 АУФ волчара 🐺 Твой сайз: %s см',
                        $penis->getSize()
                    ),
                    'reply_to_message_id' => $messageId,
                ]);
                return;
        }
    }

    private function sendMessageDiff(
        TelegramAPI $telegram,
        Penis $penis,
        SpinPenisResult $spinResult,
        int $chatId,
        int $messageId
    ): void {
        switch ($spinResult->getDiffSize()) {
            case -5:
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Хмммм....',
                ]);

                $telegram->sendChatAction([
                    'chat_id' => $chatId,
                    'action' => Actions::TYPING,
                ]);
                sleep(1);

                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Мдаа...',
                ]);

                $telegram->sendChatAction([
                    'chat_id' => $chatId,
                    'action' => Actions::TYPING,
                ]);
                sleep(2);

                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => sprintf(
                        'У тебя -5 петушара🐓 И я не шучу. Твой сайз: %s см',
                        $penis->getSize()
                    ),
                    'reply_to_message_id' => $messageId,
                ]);
                return;
            case -4:
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => sprintf(
                        '-4 не переживай до свадьбы отрастет 🤥 Твой сайз: %s см',
                        $penis->getSize()
                    ),
                    'reply_to_message_id' => $messageId,
                ]);
                return;
            case -3:
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => sprintf(
                        '-3 это хуже чем +1 🤡 Твой сайз: %s см',
                        $penis->getSize()
                    ),
                    'reply_to_message_id' => $messageId,
                ]);
                return;
            case -2:
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => sprintf(
                        '-2 не велика потеря бро 🥸 Твой сайз: %s см',
                        $penis->getSize()
                    ),
                    'reply_to_message_id' => $messageId,
                ]);
                return;
            case -1:
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => sprintf(
                        '-1 ты чё пидр? Да я шучу. Твой сайз: %s см',
                        $penis->getSize()
                    ),
                    'reply_to_message_id' => $messageId,
                ]);

                sleep(2);
                $telegram->sendChatAction([
                    'chat_id' => $chatId,
                    'action' => Actions::TYPING,
                ]);
                sleep(1);

                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'пидр',
                    'reply_to_message_id' => $messageId,
                ]);
                return;
        }
    }

    private function sendMessageZero(TelegramAPI $telegram, Penis $penis, int $chatId, int $messageId): void
    {
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => sprintf(
                'Чеееел... у тебя 0 см прибавилось. Твой сайз: %s см',
                $penis->getSize()
            ),
            'reply_to_message_id' => $messageId,
        ]);
    }

    private function sendMessageReset(TelegramAPI $telegram, Penis $penis, int $chatId, int $messageId): void
    {
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Так... что тут у нас...',
        ]);

        $telegram->sendChatAction([
            'chat_id' => $chatId,
            'action' => Actions::TYPING,
        ]);
        sleep(1);

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Похоже кто то сделал операцию по смену пола.',
        ]);

        $telegram->sendChatAction([
            'chat_id' => $chatId,
            'action' => Actions::TYPING,
        ]);
        sleep(2);

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => sprintf(
                'Теперь ты просто пезда. Твой сайз: %s см',
                $penis->getSize()
            ),
            'reply_to_message_id' => $messageId,
        ]);
    }
}

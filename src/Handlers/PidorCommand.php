<?php

namespace PenisBot\Handlers;

use PenisBot\Domain\Actions\Member\GetMembersAction;
use PenisBot\Domain\Actions\Penis\GetPenisAction;
use PenisBot\Domain\Actions\Penis\SpinPenisSizeAction;
use PenisBot\Domain\Actions\Penis\UpdatePenisSizeAction;
use PenisBot\Domain\Actions\Pidor\GetLastPidorAction;
use PenisBot\Domain\Actions\Pidor\IncrementCountPidorAction;
use PenisBot\Domain\Actions\Pidor\NeedSkipSpinPidorAction;
use PenisBot\Domain\Actions\Pidor\SpinPidorAction;
use PenisBot\Domain\Actions\Pidor\SpinSetPidorAction;
use PenisBot\Locker;
use Telegram\Bot\Actions;
use Telegram\Bot\Api as TelegramAPI;
use Telegram\Bot\Objects\Update;

class PidorCommand implements HandlerInterface
{
    public const COMMAND_NAME = '/pidor';

    private NotFoundPenis $notFoundPenis;
    private Error $error;
    private GetMembersAction $getMembers;
    private NeedSkipSpinPidorAction $needSkipSpinPidor;
    private GetLastPidorAction $getLastPidor;
    private SpinPidorAction $spinPidor;
    private SpinPenisSizeAction $spinPenisSize;
    private GetPenisAction $getPenis;
    private UpdatePenisSizeAction $updatePenisSize;
    private SpinSetPidorAction $spinSetPidor;
    private IncrementCountPidorAction $incrementCountPidor;
    private Locker $locker;

    public function __construct(
        NotFoundPenis $notFoundPenis,
        Error $error,
        GetMembersAction $getMembers,
        NeedSkipSpinPidorAction $needSkipSpinPidor,
        GetLastPidorAction $getLastPidor,
        SpinPidorAction $spinPidor,
        SpinPenisSizeAction $spinPenisSize,
        GetPenisAction $getPenis,
        UpdatePenisSizeAction $updatePenisSize,
        SpinSetPidorAction $spinSetPidor,
        IncrementCountPidorAction $incrementCountPidor,
        Locker $locker
    )
    {
        $this->notFoundPenis = $notFoundPenis;
        $this->error = $error;
        $this->getMembers = $getMembers;
        $this->needSkipSpinPidor = $needSkipSpinPidor;
        $this->getLastPidor = $getLastPidor;
        $this->spinPidor = $spinPidor;
        $this->spinPenisSize = $spinPenisSize;
        $this->getPenis = $getPenis;
        $this->updatePenisSize = $updatePenisSize;
        $this->spinSetPidor = $spinSetPidor;
        $this->incrementCountPidor = $incrementCountPidor;
        $this->locker = $locker;
    }

    public function process(TelegramAPI $telegram, Update $update): void
    {
        $chatId = $update->getMessage()->getChat()->getId();
        $lockKey = 'pidor' . $chatId;

        if ($this->locker->issetLock($lockKey)) {
            return;
        }

        $this->locker->lock($lockKey);

        if ($this->needSkipSpinPidor->needSkipSpinPidor($chatId)) {
            $lastPidor = $this->getLastPidor->getLastPidor($chatId);

            $telegram->sendMessage([
                'chat_id' => $chatId,
                'disable_notification' => true,
                'text' => sprintf(
                'На сегодня пидоров хватит. Если чё пидор сегодня @%s',
                    $lastPidor->getUsername(),
                ),
            ]);

            return;
        }

        $members = $this->getMembers->getMembersWithPenisForSpin($chatId);

        if (count($members) === 0) {
            $this->notFoundPenis->process($telegram, $update);
            return;
        }

        $member = $this->spinPidor->spinPidor($members);
        $penis = $this->getPenis->getPenisByMember($member);

        if ($penis === null) {
            $this->error->process($telegram, $update);
            return;
        }

        $spinPenisResult = $this->spinPenisSize->spinDiffPenisSize($penis);

        if (!$spinPenisResult->isDiff()) {
            $this->error->process($telegram, $update);
            return;
        }

        $spinSetPidorResult = $this->spinSetPidor->spinSetPidor();

        if ($spinSetPidorResult->isSecret() === false) {
            // Обновляем данные если сет секретный
            $penis = $this->updatePenisSize->updatePenisSize($penis, $spinPenisResult, false);

            $this->incrementCountPidor->incrementCountPidor($member);
        }

        $set = [];

        if ($spinSetPidorResult->isFirst()) {
            $set = $this->firstSet($chatId);
        }
        if ($spinSetPidorResult->isSecond()) {
            $set = $this->secondSet($chatId);
        }
        if ($spinSetPidorResult->isThird()) {
            $set = $this->thirdSet($chatId);
        }
        if ($spinSetPidorResult->isSecret()) {
            $set = $this->ernestSet($chatId);
        }

        foreach ($set as $message) {
            if (isset($message['end']) && $message['end'] === true) {
                $message['text'] = sprintf(
                    $message['text'],
                    $member->getUsername(),
                    $spinPenisResult->getDiffSize(),
                    $penis->getSize()
                );

                $telegram->sendMessage($message);
            } else {
                $telegram->sendMessage($message);

                $telegram->sendChatAction([
                    'chat_id' => $chatId,
                    'action' => Actions::TYPING,
                ]);
                sleep(1);
            }
        }

        $this->locker->unlock($lockKey);
    }

    private function firstSet(int $chatId): array
    {
        return [
            [
                'chat_id' => $chatId,
                'text' => 'Разворачиваю сервис по поиску пидорасов ✈️',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'ping global.pidoras.com...',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'pong 64 bytes from zebal pingovat\'...',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Делаю запрос на поиск 🔎',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'О что-то нашлось...',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Ага пидор дня @%s! Твой хуй стал короче на %s см. Теперь он %s см.',
                'end' => true,
            ],
        ];
    }

    private function secondSet(int $chatId): array
    {
        return [
            [
                'chat_id' => $chatId,
                'text' => 'Начинаю расследование️ 🕵️‍♂️',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Отправляю запрос в антипидорскую службу 📩',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Уточняю координаты объекта 📍',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Избавляюсь от свидетелей 🥷',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Попався пидор. Мой попу @%s. Твой хуй стал короче на %s см. Теперь он %s см.',
                'end' => true,
            ],
        ];
    }

    private function thirdSet(int $chatId): array
    {
        return [
            [
                'chat_id' => $chatId,
                'text' => 'Сча поищу.',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Первым делом зайду в бар 🍺',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Теперь погнал в клуб 🎉',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Ооо тут ещё казино есть 🎰',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Ёбаный рот этого казино... А? Что? Пидора надо найти? Сча.',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Пусть пидором будет @%s. Твой хуй стал короче на %s см. Теперь он %s см.',
                'end' => true,
            ],
        ];
    }

    private function ernestSet(int $chatId): array
    {
        return [
            [
                'chat_id' => $chatId,
                'text' => 'Бляяя опять работать...',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Ну давай посмотрим, что у нас тут есть.',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Тут написано что Ернест пидор.',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Ну ладно ладно. Сча нормально поищу.',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Я ничего не нашел. Кароч Эрнест пидор. Отъебитесь от меня 🖕',
            ],
        ];
    }
}

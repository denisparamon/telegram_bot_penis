<?php

namespace PenisBot\Handlers;

use PenisBot\Domain\Actions\Gigachad\GetLastGigachadAction;
use PenisBot\Domain\Actions\Gigachad\IncrementCountGigachadAction;
use PenisBot\Domain\Actions\Gigachad\NeedSkipSpinGigachadAction;
use PenisBot\Domain\Actions\Gigachad\SpinGigachadAction;
use PenisBot\Domain\Actions\Gigachad\SpinSetGigachadAction;
use PenisBot\Domain\Actions\Member\GetMembersAction;
use PenisBot\Domain\Actions\Penis\GetPenisAction;
use PenisBot\Domain\Actions\Penis\SpinPenisSizeAction;
use PenisBot\Domain\Actions\Penis\UpdatePenisSizeAction;
use PenisBot\Locker;
use Telegram\Bot\Actions;
use Telegram\Bot\Api as TelegramAPI;
use Telegram\Bot\Objects\Update;

class GigachadCommand implements HandlerInterface
{
    public const COMMAND_NAME = '/gigachad';

    private NotFoundPenis $notFoundPenis;
    private Error $error;
    private GetMembersAction $getMembers;
    private NeedSkipSpinGigachadAction $needSkipSpinGigachad;
    private GetLastGigachadAction $getLastGigachad;
    private SpinGigachadAction $spinGigachad;
    private SpinPenisSizeAction $spinPenisSize;
    private GetPenisAction $getPenis;
    private UpdatePenisSizeAction $updatePenisSize;
    private SpinSetGigachadAction $spinSetGigachad;
    private IncrementCountGigachadAction $incrementCountGigachad;
    private Locker $locker;

    public function __construct(
        NotFoundPenis $notFoundPenis,
        Error $error,
        GetMembersAction $getMembers,
        NeedSkipSpinGigachadAction $needSkipSpinGigachad,
        GetLastGigachadAction $getLastGigachad,
        SpinGigachadAction $spinGigachad,
        SpinPenisSizeAction $spinPenisSize,
        GetPenisAction $getPenis,
        UpdatePenisSizeAction $updatePenisSize,
        SpinSetGigachadAction $spinSetGigachad,
        IncrementCountGigachadAction $incrementCountGigachad,
        Locker $locker
    )
    {
        $this->notFoundPenis = $notFoundPenis;
        $this->error = $error;
        $this->getMembers = $getMembers;
        $this->needSkipSpinGigachad = $needSkipSpinGigachad;
        $this->getLastGigachad = $getLastGigachad;
        $this->spinGigachad = $spinGigachad;
        $this->spinPenisSize = $spinPenisSize;
        $this->getPenis = $getPenis;
        $this->updatePenisSize = $updatePenisSize;
        $this->spinSetGigachad = $spinSetGigachad;
        $this->incrementCountGigachad = $incrementCountGigachad;
        $this->locker = $locker;
    }

    public function process(TelegramAPI $telegram, Update $update): void
    {
        $chatId = $update->getMessage()->getChat()->getId();
        $lockKey = 'gigachad' . $chatId;

        if ($this->locker->issetLock($lockKey)) {
            return;
        }

        $this->locker->lock($lockKey);

        if ($this->needSkipSpinGigachad->needSkipSpinGigachad($chatId)) {
            $lastGigachad = $this->getLastGigachad->getLastGigachad($chatId);

            $telegram->sendMessage([
                'chat_id' => $chatId,
                'disable_notification' => true,
                'text' => sprintf(
                'Сегодня альфа самец @%s и никто его не заменит!',
                    $lastGigachad->getUsername(),
                ),
            ]);

            return;
        }

        $members = $this->getMembers->getMembersWithPenisForSpin($chatId);

        if (count($members) === 0) {
            $this->notFoundPenis->process($telegram, $update);
            return;
        }


        $member = $this->spinGigachad->spinGigachad($members);
        $penis = $this->getPenis->getPenisByMember($member);

        if ($penis === null) {
            $this->error->process($telegram, $update);
            return;
        }

        $spinPenisResult = $this->spinPenisSize->spinAddPenisSize($penis);

        if (!$spinPenisResult->isAdd()) {
            $this->error->process($telegram, $update);
            return;
        }

        $spinSetGigachadResult = $this->spinSetGigachad->spinSetGigachad();

        if ($spinSetGigachadResult->isSecret() === false) {
            // Обновляем данные если сет секретный
            $this->incrementCountGigachad->incrementCountGigachad($member);

            $penis = $this->updatePenisSize->updatePenisSize($penis, $spinPenisResult, false);
        }

        $set = [];

        if ($spinSetGigachadResult->isFirst()) {
            $set = $this->firstSet($chatId);
        }
        if ($spinSetGigachadResult->isSecond()) {
            $set = $this->secondSet($chatId);
        }
        if ($spinSetGigachadResult->isThird()) {
            $set = $this->thirdSet($chatId);
        }
        if ($spinSetGigachadResult->isSecret()) {
            $set = $this->secretSet($chatId);
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
                'text' => 'Жи есть! Сэйчас поищем кразавчика ☝️',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Эу! У кого камри 3.5? 🏎',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Может хотябы приора есть? 🚗',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Похуй. Сэйчас у пацанов поспрашиваю кто? что? как?',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Воу воу воу паприветсвуйте хасанчика @%s!🔥 Твой хуй стал длиннее на %s см. Теперь он %s см.',
                'end' => true,
            ],
        ];
    }

    private function secondSet(int $chatId): array
    {
        return [
            [
                'chat_id' => $chatId,
                'text' => 'Хочешь узнать кто сегодня альфа самец? 🤨',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Этот в цирке выступает... 🎪',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Тот запомнить не может. Тупой ссука.',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'А у этого хуя даже нет 🔫',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'А вот и он наш волчара альфа самец @%s!🐺🔥 Твой хуй стал длиннее на %s см. Теперь он %s см.',
                'end' => true,
            ],
        ];
    }

    private function thirdSet(int $chatId): array
    {
        return [
            [
                'chat_id' => $chatId,
                'text' => 'Хмм... Кто же сегодня гигачад?',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Провожу фотосессию 📸',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Обрабатываю снимки 📀',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Анализирую фотографии 🔬',
            ],
            [
                'chat_id' => $chatId,
                'text' => 'Синтезирую ДНК 🧬',
            ],
            [
                'chat_id' => $chatId,
                'text' => '@%s бля реально гигачад. Твой хуй стал длиннее на %s см. Теперь он %s см.',
                'end' => true,
            ],
        ];
    }

    private function secretSet(int $chatId): array
    {
        return [
            [
                'chat_id' => $chatId,
                'text' => 'Я блять тут альфа! +10 000 к моему хую! Так что пошли нахуй 👿',
            ],
        ];
    }
}

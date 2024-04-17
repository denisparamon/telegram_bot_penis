<?php

namespace PenisBot\Domain\Actions\Pidor;

use DateTimeImmutable;
use PenisBot\Domain\Repositories\PidorsRepository;

class NeedSkipSpinPidorAction
{
    private PidorsRepository $pidorsRepository;

    public function __construct(PidorsRepository $pidorsRepository)
    {
        $this->pidorsRepository = $pidorsRepository;
    }

    public function needSkipSpinPidor(int $chatId): bool
    {
        $date = $this->pidorsRepository->getLastPidorDateTime($chatId);

        if ($date === null) {
            return false;
        }

        $currentDateTime = new DateTimeImmutable();

        return $date->format('d') === $currentDateTime->format('d');
    }
}

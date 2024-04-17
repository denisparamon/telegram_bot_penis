<?php

namespace PenisBot\Domain\Actions\Gigachad;

use DateTimeImmutable;
use PenisBot\Domain\Repositories\GigachadsRepository;

class NeedSkipSpinGigachadAction
{
    private GigachadsRepository $gigachadsRepository;

    public function __construct(GigachadsRepository $gigachadsRepository)
    {
        $this->gigachadsRepository = $gigachadsRepository;
    }

    public function needSkipSpinGigachad(int $chatId): bool
    {
        $date = $this->gigachadsRepository->getLastGigachadDateTime($chatId);

        if ($date === null) {
            return false;
        }

        $currentDateTime = new DateTimeImmutable();

        return $date->format('d') === $currentDateTime->format('d');
    }
}

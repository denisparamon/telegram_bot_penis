<?php

namespace PenisBot\Domain\Actions\Pidor;

use DateTimeImmutable;
use PenisBot\Domain\Elements\Member;
use PenisBot\Domain\Repositories\MembersRepository;
use PenisBot\Domain\Repositories\PidorsRepository;

class GetLastPidorAction
{
    private PidorsRepository $pidorsRepository;
    private MembersRepository $membersRepository;

    public function __construct(PidorsRepository $pidorsRepository, MembersRepository $membersRepository)
    {
        $this->pidorsRepository = $pidorsRepository;
        $this->membersRepository = $membersRepository;
    }

    public function getLastPidor(int $chatId): Member
    {
        $lastPidorUserTelegramId = $this->pidorsRepository->getLastPidorUserTelegramId($chatId);

        return $this->membersRepository->getByTelegramIds($lastPidorUserTelegramId, $chatId);
    }
}

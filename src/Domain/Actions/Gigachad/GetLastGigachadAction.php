<?php

namespace PenisBot\Domain\Actions\Gigachad;

use PenisBot\Domain\Elements\Member;
use PenisBot\Domain\Repositories\MembersRepository;
use PenisBot\Domain\Repositories\GigachadsRepository;

class GetLastGigachadAction
{
    private GigachadsRepository $gigachadsRepository;
    private MembersRepository $membersRepository;

    public function __construct(GigachadsRepository $gigachadsRepository, MembersRepository $membersRepository)
    {
        $this->gigachadsRepository = $gigachadsRepository;
        $this->membersRepository = $membersRepository;
    }

    public function getLastGigachad(int $chatId): Member
    {
        $lastGigachadUserTelegramId = $this->gigachadsRepository->getLastGigachadUserTelegramId($chatId);

        return $this->membersRepository->getByTelegramIds($lastGigachadUserTelegramId, $chatId);
    }
}

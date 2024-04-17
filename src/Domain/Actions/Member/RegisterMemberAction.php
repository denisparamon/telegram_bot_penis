<?php

namespace PenisBot\Domain\Actions\Member;

use PenisBot\Domain\Repositories\MembersRepository;

class RegisterMemberAction
{
    private MembersRepository $membersRepository;

    public function __construct(MembersRepository $membersRepository)
    {
        $this->membersRepository = $membersRepository;
    }

    public function registerMember(int $userTelegramId, int $chatTelegramId, string $username): void
    {
        $this->membersRepository->addNewMember($userTelegramId, $chatTelegramId, $username);
    }
}

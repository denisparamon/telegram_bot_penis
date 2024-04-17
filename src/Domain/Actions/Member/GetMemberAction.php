<?php

namespace PenisBot\Domain\Actions\Member;

use PenisBot\Domain\Elements\Member;
use PenisBot\Domain\Repositories\MembersRepository;

class GetMemberAction
{
    private MembersRepository $membersRepository;

    public function __construct(MembersRepository $membersRepository)
    {
        $this->membersRepository = $membersRepository;
    }

    public function getMember(int $userTelegramId, int $chatTelegramId): ?Member
    {
        return $this->membersRepository->getByTelegramIds($userTelegramId, $chatTelegramId);
    }
}

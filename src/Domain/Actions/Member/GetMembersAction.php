<?php

namespace PenisBot\Domain\Actions\Member;

use DateInterval;
use DateTimeImmutable;
use PenisBot\Domain\Elements\Member;
use PenisBot\Domain\Repositories\MembersRepository;

class GetMembersAction
{
    private MembersRepository $membersRepository;

    public function __construct(MembersRepository $membersRepository)
    {
        $this->membersRepository = $membersRepository;
    }

    /**
     * @return Member[]
     */
    public function getMembersWithPenisForSpin(int $chatTelegramId): array
    {
        $startCurrentDateTime = (new DateTimeImmutable())->setTime(0, 0);

        return $this->membersRepository->getMembersWithPenisByChatId($chatTelegramId, $startCurrentDateTime);
    }
}

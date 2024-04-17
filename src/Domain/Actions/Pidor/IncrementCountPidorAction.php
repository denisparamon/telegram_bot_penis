<?php

namespace PenisBot\Domain\Actions\Pidor;

use PenisBot\Domain\Elements\Member;
use PenisBot\Domain\Repositories\PidorsRepository;

class IncrementCountPidorAction
{
    private PidorsRepository $pidorsRepository;

    public function __construct(PidorsRepository $pidorsRepository)
    {
        $this->pidorsRepository = $pidorsRepository;
    }

    public function incrementCountPidor(Member $member): void
    {
        $count = $this->pidorsRepository->getCountPidor($member->getUserTelegramId(), $member->getChatTelegramId());

        if ($count === null) {
            $this->pidorsRepository->addPidor($member->getUserTelegramId(), $member->getChatTelegramId(), 1);
        } else {
            $this->pidorsRepository->updatePidor(
                $member->getUserTelegramId(),
                $member->getChatTelegramId(),
                $count + 1
            );
        }
    }
}

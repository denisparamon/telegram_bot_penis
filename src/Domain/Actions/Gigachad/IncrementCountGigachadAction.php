<?php

namespace PenisBot\Domain\Actions\Gigachad;

use PenisBot\Domain\Elements\Member;
use PenisBot\Domain\Repositories\GigachadsRepository;

class IncrementCountGigachadAction
{
    private GigachadsRepository $gigachadsRepository;

    public function __construct(GigachadsRepository $gigachadsRepository)
    {
        $this->gigachadsRepository = $gigachadsRepository;
    }

    public function incrementCountGigachad(Member $member): void
    {
        $count = $this->gigachadsRepository->getCountGigachad($member->getUserTelegramId(), $member->getChatTelegramId());

        if ($count === null) {
            $this->gigachadsRepository->addGigachad($member->getUserTelegramId(), $member->getChatTelegramId(), 1);
        } else {
            $this->gigachadsRepository->updateGigachad(
                $member->getUserTelegramId(),
                $member->getChatTelegramId(),
                $count + 1
            );
        }
    }
}

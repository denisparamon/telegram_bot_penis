<?php

namespace PenisBot\Domain\Actions\Penis;

use DateInterval;
use DateTimeImmutable;
use PenisBot\Domain\Elements\Member;
use PenisBot\Domain\Elements\Penis;
use PenisBot\Domain\Repositories\PenisesRepository;

class RegisterPenisAction
{
    private const DEFAULT_SIZE = 5;

    private PenisesRepository $penisesRepository;

    public function __construct(PenisesRepository $penisesRepository)
    {
        $this->penisesRepository = $penisesRepository;
    }

    public function registerPenis(Member $member): Penis
    {
        $twoDaysAgo = (new DateTimeImmutable())->sub(new DateInterval('P2D'));

        $this->penisesRepository->addNewPenis($member, static::DEFAULT_SIZE, $twoDaysAgo);

        return $this->penisesRepository->getPenisByMember($member);
    }
}

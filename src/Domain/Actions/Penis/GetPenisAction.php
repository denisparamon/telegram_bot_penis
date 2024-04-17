<?php

namespace PenisBot\Domain\Actions\Penis;

use PenisBot\Domain\Elements\Member;
use PenisBot\Domain\Elements\Penis;
use PenisBot\Domain\Repositories\PenisesRepository;

class GetPenisAction
{
    private PenisesRepository $penisesRepository;

    public function __construct(PenisesRepository $penisesRepository)
    {
        $this->penisesRepository = $penisesRepository;
    }

    public function getPenisByMember(Member $member): ?Penis
    {
        return $this->penisesRepository->getPenisByMember($member);
    }
}

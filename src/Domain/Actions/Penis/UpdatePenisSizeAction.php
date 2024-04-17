<?php

namespace PenisBot\Domain\Actions\Penis;

use DateInterval;
use DateTimeImmutable;
use PenisBot\Domain\Elements\Member;
use PenisBot\Domain\Elements\Penis;
use PenisBot\Domain\Elements\SpinPenisResult;
use PenisBot\Domain\Repositories\PenisesRepository;

class UpdatePenisSizeAction
{
    private PenisesRepository $penisesRepository;

    public function __construct(PenisesRepository $penisesRepository)
    {
        $this->penisesRepository = $penisesRepository;
    }

    public function updatePenisSize(
        Penis $penis,
        SpinPenisResult $spinPenisResult,
        bool $needUpdateLastUpdateDate
    ): Penis {
        if ($spinPenisResult->isReset()) {
            $this->penisesRepository->updatePenis(
                $penis,
                0,
                $needUpdateLastUpdateDate
            );
        } else {
            $this->penisesRepository->updatePenis(
                $penis,
                $penis->getSize() + $spinPenisResult->getDiffSize(),
                $needUpdateLastUpdateDate
            );
        }

        return $this->penisesRepository->getPenisByMember($penis->getMember());
    }
}

<?php

namespace PenisBot\Domain\Actions\Penis;

use DateTimeImmutable;
use PenisBot\Domain\Elements\Penis;

class NeedSkipSpinPenisSizeAction
{
    public function needSkipSpinPenisSize(Penis $penis): bool
    {
        $currentDateTime = new DateTimeImmutable();

        return $penis->getLastUpdateAt()->format('d') === $currentDateTime->format('d');
    }
}

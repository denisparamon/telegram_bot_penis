<?php

namespace PenisBot\Domain\Actions\Pidor;

use PenisBot\Domain\Elements\SpinSetResult;

class SpinSetPidorAction
{
    public function spinSetPidor(): SpinSetResult
    {
        $randInt = random_int(0, 10_000_000);

        if ($randInt <= 300_000) {
            return new SpinSetResult(SpinSetResult::SECRET);
        }
        if ($randInt > 300_000 && $randInt <= 3_300_000) {
            return new SpinSetResult(SpinSetResult::FIRST);
        }
        if ($randInt > 3_300_000 && $randInt <= 6_600_000) {
            return new SpinSetResult(SpinSetResult::SECOND);
        }

        return new SpinSetResult(SpinSetResult::THIRD);
    }
}

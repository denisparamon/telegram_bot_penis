<?php

namespace PenisBot\Domain\Actions\Gigachad;

use PenisBot\Domain\Elements\SpinSetResult;

class SpinSetGigachadAction
{
    public function spinSetGigachad(): SpinSetResult
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

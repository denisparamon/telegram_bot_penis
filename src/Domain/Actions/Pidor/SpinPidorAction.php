<?php

namespace PenisBot\Domain\Actions\Pidor;

use PenisBot\Domain\Elements\Member;

class SpinPidorAction
{
    /**
     * @param Member[] $members
     * @return Member
     */
    public function spinPidor(array $members): Member
    {
        $randomInt = random_int(0, (count($members) * 1_000_000) - 1);

        return $members[(int)floor($randomInt / 1_000_000)];
    }
}

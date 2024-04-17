<?php

namespace PenisBot\Domain\Actions\Gigachad;

use PenisBot\Domain\Elements\Member;

class SpinGigachadAction
{
    /**
     * @param Member[] $members
     * @return Member
     */
    public function spinGigachad(array $members): Member
    {
        $randomInt = random_int(0, (count($members) * 1_000_000) - 1);

        return $members[(int)floor($randomInt / 1_000_000)];
    }
}

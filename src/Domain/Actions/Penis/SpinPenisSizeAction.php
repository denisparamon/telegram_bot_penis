<?php

namespace PenisBot\Domain\Actions\Penis;

use DateInterval;
use DateTimeImmutable;
use PenisBot\Domain\Elements\Member;
use PenisBot\Domain\Elements\Penis;
use PenisBot\Domain\Elements\SpinPenisResult;
use PenisBot\Domain\Repositories\PenisesRepository;

class SpinPenisSizeAction
{
    public function spinPenisSize(Penis $penis): SpinPenisResult
    {
        return $this->calculateResult($penis);
    }

    public function spinAddPenisSize(Penis $penis): SpinPenisResult
    {
        return $this->calculateResult($penis, true);
    }

    public function spinDiffPenisSize(Penis $penis): SpinPenisResult
    {
        return $this->calculateResult($penis, false, true);
    }

    private function calculateResult(Penis $penis, bool $needAdd = false, bool $needDiff = false): SpinPenisResult
    {
        // Если надо прибавить, то в этом случае мы не скидываем размер
        if ($needAdd === false && $this->needReset($penis)) {
            return new SpinPenisResult(SpinPenisResult::RESET, 0);
        }

        $min = $needAdd ? 0 : -10_000_000;
        $max = $needDiff ? 0 : 50_000_000;

        // генерим от -10 до 50, чтобы чаще прибавлять, чем убавлять
        $randomInt = random_int($min, $max);

        if ($randomInt > 40_000_000) {
            $randomInt -= 40_000_000;
        }
        if ($randomInt > 30_000_000) {
            $randomInt -= 30_000_000;
        }
        if ($randomInt > 20_000_000) {
            $randomInt -= 20_000_000;
        }
        if ($randomInt > 10_000_000) {
            $randomInt -= 10_000_000;
        }

        $multiplicator = $randomInt > 0 ? 1 : -1;

        $size = $this->calculateRandSize($randomInt * $multiplicator);

        if ($size > 0) {
            $size *= $multiplicator;
        }

        if ($size < 0) {
            return new SpinPenisResult(SpinPenisResult::DIFF, $size);
        }

        if ($size === 0) {
            return new SpinPenisResult(SpinPenisResult::ZERO, $size);
        }

        return new SpinPenisResult(SpinPenisResult::ADD, $size);
    }

    private function needReset(Penis $penis): bool
    {
        return random_int($penis->getSize() * 10_000, 10_000_000) > 9_900_000;
    }

    private function calculateRandSize(int $randomInt): int
    {
        if ($randomInt > 500_000 && $randomInt <= 4_000_000) {
            return 1; // (4 000 000 - 500 000) / 100 000 = 35%
        }

        if ($randomInt > 4_000_000 && $randomInt <= 6_500_000) {
            return 2; // (6 500 000 - 4 000 000) / 100 000 = 25%
        }

        if ($randomInt > 6_500_000 && $randomInt <= 8_000_000) {
            return 3; // (6 500 000 - 8 000 000) / 100 000 = 15%
        }

        if ($randomInt > 8_000_000 && $randomInt <= 9_500_000) {
            return 4; // (8 000 000 - 9 500 000) / 100 000 = 15%
        }

        if ($randomInt > 9_500_000 && $randomInt <= 10_000_000) {
            return 5; // (9 500 000 - 10 000 000) / 100 000 = 15%
        }

        return 0; // 500 000 / 100 000 = 5%
    }
}

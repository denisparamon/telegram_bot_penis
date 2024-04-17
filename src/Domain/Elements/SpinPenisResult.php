<?php

namespace PenisBot\Domain\Elements;

class SpinPenisResult
{
    public const ADD = 'add';
    public const DIFF = 'diff';
    public const RESET = 'reset';
    public const ZERO = 'zero';

    private string $result;
    private int $diffSize;

    public function __construct(string $result, int $diffSize)
    {
        $this->result = $result;
        $this->diffSize = $diffSize;
    }

    public function isAdd(): bool
    {
        return $this->result === static::ADD;
    }

    public function isDiff(): bool
    {
        return $this->result === static::DIFF;
    }

    public function isReset(): bool
    {
        return $this->result === static::RESET;
    }

    public function isZero(): bool
    {
        return $this->result === static::ZERO;
    }

    public function getDiffSize(): int
    {
        return $this->diffSize;
    }
}

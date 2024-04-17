<?php

namespace PenisBot\Domain\Elements;

class SpinSetResult
{
    public const FIRST = 'first';
    public const SECOND = 'second';
    public const THIRD = 'third';
    public const SECRET = 'secret';

    private string $result;

    public function __construct(string $result)
    {
        $this->result = $result;
    }

    public function isFirst(): bool
    {
        return $this->result === static::FIRST;
    }

    public function isSecond(): bool
    {
        return $this->result === static::SECOND;
    }

    public function isThird(): bool
    {
        return $this->result === static::THIRD;
    }

    public function isSecret(): bool
    {
        return $this->result === static::SECRET;
    }
}

<?php

namespace PenisBot\Domain\Elements;

class GigachadForTop
{
    private int $count;
    private string $username;

    public function __construct(int $count, string $username) {
        $this->count = $count;
        $this->username = $username;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}

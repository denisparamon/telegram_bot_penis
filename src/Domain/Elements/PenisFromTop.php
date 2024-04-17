<?php

namespace PenisBot\Domain\Elements;

class PenisFromTop
{
    private int $id;
    private int $size;
    private string $username;

    public function __construct(int $id, int $size, string $username)
    {
        $this->id = $id;
        $this->size = $size;
        $this->username = $username;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}

<?php

namespace PenisBot\Domain\Elements;

class Member
{
    private int $id;
    private int $userTelegramId;
    private int $chatTelegramId;
    private string $username;

    public function __construct(
        int $id,
        int $userTelegramId,
        int $chatTelegramId,
        string $username
    ) {
        $this->id = $id;
        $this->userTelegramId = $userTelegramId;
        $this->chatTelegramId = $chatTelegramId;
        $this->username = $username;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserTelegramId(): int
    {
        return $this->userTelegramId;
    }

    public function getChatTelegramId(): int
    {
        return $this->chatTelegramId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}

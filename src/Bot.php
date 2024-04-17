<?php

namespace PenisBot;

class Bot
{
    private string $token;
    private string $name;

    public function __construct(string $token, string $name)
    {
        $this->token = $token;
        $this->name = $name;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

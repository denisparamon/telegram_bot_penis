<?php

namespace PenisBot\Domain\Elements;

use DateTimeInterface;

class Penis
{
    private int $id;
    private int $size;
    private DateTimeInterface $lastUpdateAt;
    private Member $member;

    public function __construct(int $id, int $size, DateTimeInterface $lastUpdateAt, Member $member)
    {
        $this->id = $id;
        $this->size = $size;
        $this->lastUpdateAt = $lastUpdateAt;
        $this->member = $member;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getLastUpdateAt(): DateTimeInterface
    {
        return $this->lastUpdateAt;
    }

    public function getMember(): Member
    {
        return $this->member;
    }
}

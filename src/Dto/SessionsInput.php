<?php

namespace App\Dto;

class SessionsInput
{
    public ?\DateTimeImmutable $startAt;
    public ?\DateTimeImmutable $endAt;
    public int $terminalId;

    /** @var AllEvent[] $allEvent */
    public array $allEvent;
}
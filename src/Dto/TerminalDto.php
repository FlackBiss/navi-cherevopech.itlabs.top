<?php

namespace App\Dto;

final class TerminalDto
{
    public ?int $node = null;
    public ?int $area = null;

    public function getNode(): ?int
    {
        return $this->node;
    }

    public function getArea(): ?int
    {
        return $this->area;
    }
}
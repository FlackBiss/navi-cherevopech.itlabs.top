<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class PointDto
{
    #[Assert\NotNull]
    public ?float $x = null;

    #[Assert\NotNull]
    public ?float $y = null;

    #[Assert\NotNull]
    public ?int $floor = null;

    /**
     * @return int|null
     */
    public function getFloor(): ?int
    {
        return $this->floor;
    }

    public function getX(): ?float
    {
        return $this->x;
    }

    public function getY(): ?float
    {
        return $this->y;
    }
}
<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class AreaDto
{
    public ?array $points = null;

    #[Assert\NotNull]
    public ?int $floor = null;

    /**
     * @return int|null
     */
    public function getFloor(): ?int
    {
        return $this->floor;
    }

    /**
     * @return array|null
     */
    public function getPoints(): ?array
    {
        return $this->points;
    }
}
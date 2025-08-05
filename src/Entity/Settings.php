<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Repository\SettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[Get(uriTemplate: '/settings')]
#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $timeImages = null;

    #[ORM\Column]
    private ?int $timeDelay = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private ?int $timePopup = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimeImages(): ?int
    {
        return $this->timeImages;
    }

    public function setTimeImages(int $timeImages): static
    {
        $this->timeImages = $timeImages;

        return $this;
    }

    public function getTimeDelay(): ?int
    {
        return $this->timeDelay;
    }

    public function setTimeDelay(int $timeDelay): static
    {
        $this->timeDelay = $timeDelay;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getTimePopup(): ?int
    {
        return $this->timePopup;
    }

    public function setTimePopup(int $timePopup): static
    {
        $this->timePopup = $timePopup;

        return $this;
    }
}

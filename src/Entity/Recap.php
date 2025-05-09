<?php

namespace App\Entity;

use App\enums\GameStatus;
use App\Repository\RecapRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RecapRepository::class)]
#[ORM\UniqueConstraint(name: 'unique_player_game', columns: ['player_id', 'game_id'])]
class Recap
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'recaps')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['recap:read'])]
    private ?Player $player = null;

    #[ORM\ManyToOne(inversedBy: 'recaps')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['recap:read'])]
    private ?Game $game = null;

    #[ORM\Column(nullable: false, enumType: GameStatus::class)]
    #[Groups(['recap:read'])]
    private ?GameStatus $status = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['recap:read'])]
    private ?int $rating = null;

    #[ORM\Column]
    #[Groups(['recap:read'])]
    private ?\DateTimeImmutable $addedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['recap:read'])]
    private ?\DateTime $lastUpdated = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): static
    {
        $this->player = $player;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getStatus(): ?GameStatus
    {
        return $this->status;
    }

    public function setStatus(?GameStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function setAddedAt(\DateTimeImmutable $addedAt): static
    {
        $this->addedAt = $addedAt;

        return $this;
    }

    public function getLastUpdated(): ?\DateTime
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated(\DateTime $lastUpdated): static
    {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }
}

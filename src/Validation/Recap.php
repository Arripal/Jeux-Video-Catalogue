<?php

namespace App\Validation;

use App\Entity\Game;
use App\Entity\Player;
use App\enums\GameStatus;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validation\Constraints as CustomAssert;

#[CustomAssert\UniqueRecap(groups: ['recap:add'])]
class Recap
{
    #[Assert\NotNull(message: "Vous devez fournir un profil de joueur.")]
    private Player $player;

    #[Assert\NotNull(message: "Vous devez fournir le jeu.")]
    private Game $game;

    #[Assert\NotNull(message: "Vous devez fournir un statut pour le jeu.")]
    #[Assert\Choice(
        callback: [GameStatus::class, 'cases'],
        message: "Le statut de jeu est invalide."
    )]
    private GameStatus $status;

    #[Assert\Range(
        min: 0,
        max: 5,
        notInRangeMessage: "La note doit être comprise entre {{ min }} et {{ max }}."
    )]
    private ?int $rating;

    public function __construct(Player $player,  Game $game,  GameStatus $status,  ?int $rating)
    {
        $this->player = $player;
        $this->game = $game;
        $this->status = $status;
        $this->rating = $rating;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function setPlayer(Player $player)
    {
        $this->player = $player;
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function setGame(Game $game)
    {
        $this->game = $game;
    }
}

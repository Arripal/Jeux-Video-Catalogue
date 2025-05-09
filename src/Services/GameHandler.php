<?php

namespace App\Services;

use App\Entity\Game;
use App\Exception\GameExistsException;
use App\Repository\GameRepository;
use App\Validation\Game as ValidationGame;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class GameHandler
{
    public function __construct(
        private SerializerInterface $serializer,
        private Validation $validation,
        private EntityManagerInterface $entity_manager,
        private GameRepository $game_repository
    ) {}

    public function create(ValidationGame $validation_game): Game
    {

        $game = new Game();

        $game->setApiID($validation_game->getApiId())
            ->setDevelopers($validation_game->getDevelopers())
            ->setGenres($validation_game->getGenres())
            ->setFranchise($validation_game->getFranchise())
            ->setGlobalRating($validation_game->getGlobalRating())
            ->setPlateforms($validation_game->getPlateforms())
            ->setPublisher($validation_game->getPublisher())
            ->setRatingCount($validation_game->getRatingCount())
            ->setReleaseDate($validation_game->getReleaseDate())
            ->setTitle($validation_game->getTitle());

        return $game;
    }

    public function validateAndCreate(array $data): Game
    {
        $valid_game = $this->validation->validate(ValidationGame::class, $data);

        $game = $this->create($valid_game);

        return $game;
    }

    public function find(string $api_game_id): Game
    {
        $existing_game = $this->game_repository->findOneBy([
            'apiID' => $api_game_id
        ]);

        return $existing_game;
    }
}

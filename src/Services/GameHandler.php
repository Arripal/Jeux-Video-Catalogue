<?php

namespace App\Services;

use App\Entity\Game;
use App\Exception\GameExistsException;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class GameHandler
{
    public function __construct(
        private SerializerInterface $serializer,
        private DenormalizerInterface $denormalizer,
        private Validation $validation,
        private EntityManagerInterface $entity_manager,
        private GameRepository $game_repository
    ) {}

    public function create(array $data): Game
    {

        $game = $this->denormalizer->denormalize($data, Game::class, 'array');

        return $game;
    }

    public function validateAndCreate(array $data, $groups = null): Game
    {
        $existing_game = $this->find($data['apiID']);

        if ($existing_game) {
            throw new GameExistsException("Ce jeu est déjà enregistré.");
        }

        $game = $this->create($data);

        $game = $this->validation->validate($game, null, $groups);

        return $game;
    }

    public function find(string $api_game_id): ?Game
    {
        return  $this->game_repository->findOneBy([
            'apiID' => $api_game_id
        ]);
    }
}

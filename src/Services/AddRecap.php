<?php

namespace App\Services;

use App\Entity\Game as EntityGame;
use App\Entity\Player;
use App\Exception\RecapExistingException;
use App\Repository\PlayerRepository;
use App\Repository\RecapRepository;
use App\Validation\Game;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddRecap
{

    public function __construct(private EntityManagerInterface $entity_manager, private RecapRepository $recap_repository, private Validation $validation, private SerializerInterface $serializer_interface, private PlayerRepository $player_repository, private GameHandler $game_handler, private PlayerHandler $player_handler, private ValidatorInterface $validator_interface) {}

    public function add(array $data)
    {
        $player_profile = $this->player_handler->getProfile();

        /**
         * recup les données de la requete, les injecter dans un ValidationGame pour les valider
         * ensuite créer l'entité Game et la save en db
         */
        //une exception sera levée si invalide
        $valid_game = $this->validation->validate(Game::class, $data);

        $game = $this->game_handler->create($valid_game);

        $existing_recap = $this->existingRecap($player_profile, $game);

        $this->entity_manager->persist($recap);
        $this->entity_manager->flush();
    }

    private function existingRecap(Player $player, EntityGame $game)
    {
        $existing_recap = $this->recap_repository->findOneBy([
            'player' => $player,
            'game' => $game,
        ]);

        if ($existing_recap) {
            throw new RecapExistingException('Ce jeu est déjà présent dans votre bibliothèque.');
        }

        return $existing_recap;
    }
}

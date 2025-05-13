<?php

namespace App\Services;

use App\Entity\Game;
use App\Entity\Player;
use App\Entity\Recap;
use App\enums\GameStatus;
use App\Exception\RecapExistingException;
use App\Exception\RecapNotFoundException;
use App\Exception\ValidationException;
use App\Repository\PlayerRepository;
use App\Repository\RecapRepository;
use App\Utils\FieldsHandler;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

use DateTime;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RecapHandler
{
    public function __construct(private EntityManagerInterface $entity_manager, private RecapRepository $recap_repository, private Validation $validation, private SerializerInterface $serializer_interface, private PlayerRepository $player_repository, private GameHandler $game_handler, private PlayerHandler $player_handler, private ValidatorInterface $validator_interface) {}

    public function add(array $data)
    {
        $player = $this->player_handler->getProfile();

        $game = $this->game_handler->validateAndCreate($data);

        $this->verifyExistingRecap($player, $game);

        $recap = $this->buildRecap($player, $game, $data);

        $this->saveRecap($recap, $game);

        return $game;
    }

    public function create(Player $player, Game $game, GameStatus $status, ?int $rating): Recap
    {
        $recap = new Recap();
        $recap->setPlayer($player);
        $recap->setGame($game);
        $recap->setStatus($status);
        $recap->setRating($rating);
        $recap->setAddedAt(new DateTimeImmutable());

        return $recap;
    }

    public function update(array $data, int $recap_id)
    {

        $existing_recap = $this->recap_repository->find($recap_id);

        if (!$existing_recap) {
            throw new RecapNotFoundException("Ce récapitulatif n'existe pas, impossible de le mettre à jour.");
        }

        $game_status = FieldsHandler::gameStatus($data['gameStatus']) ?? $existing_recap->getStatus();
        $rating = $data['rating'] ?? $existing_recap->getRating();

        $this->validateAndUpdate($existing_recap, $game_status, $rating);
    }

    public function save(Recap $recap): void
    {
        $this->entity_manager->persist($recap);
        $this->entity_manager->flush();
    }

    public function exists(Player $player, Game $game): ?Recap
    {
        $existing_recap = $this->recap_repository->findOneBy([
            'player' => $player,
            'game' => $game,
        ]);

        return $existing_recap;
    }

    private function validateAndCreate(Player $player, Game $game, GameStatus $game_status, ?int $rating): Recap
    {

        $recap = $this->create($player, $game, $game_status, $rating);

        $this->validation->validate($recap, null, ['recap:add']);

        return $recap;
    }

    private function validateAndUpdate(Recap $recap, GameStatus $status, ?int $rating): void
    {

        if ($rating !== $recap->getRating()) {

            $recap->setRating($rating);
        }

        if ($status === null) {
            throw new \InvalidArgumentException("Le statut du jeu est invalide.");
        }

        if ($status !== $recap->getStatus()) {
            $recap->setStatus($status);
        }

        $recap->setLastUpdated(new DateTime());

        $this->validation->validate($recap, null, ['recap:update']);

        $this->entity_manager->flush();
    }

    private function verifyExistingRecap(Player $player, Game $game): void
    {
        $existing_recap = $this->exists($player, $game);

        if ($existing_recap) {
            throw new RecapExistingException('Ce jeu est déjà présent dans votre bibliothèque.');
        }
    }



    public function getRecaps(Player $player)
    {
        $recaps = $this->recap_repository->findAllByPlayer($player);

        if (empty($recaps)) {
            throw new RecapNotFoundException("Impossible de fournir les récapitulatifs, ils n'existent pas.");
        }

        return $recaps;
    }

    public function getOneRecap(string $api_game_id): Recap
    {
        $player = $this->player_handler->getProfile();
        $recap = $this->recap_repository->findOneByApiGameId($api_game_id, $player);

        if (!$recap) {
            throw new RecapNotFoundException("Le récapitulatif est introuvable.");
        }

        return $recap;
    }


    private function buildRecap(Player $player, Game $game, array $data): Recap
    {
        $game_status = FieldsHandler::gameStatus($data['gameStatus'] ?? null);
        $rating = $data['rating'] ?? null;

        return $this->validateAndCreate($player, $game, $game_status, $rating);
    }

    private function saveRecap(Recap $recap, Game $game): void
    {
        $this->entity_manager->persist($game);
        $this->save($recap);
    }
}

<?php

namespace App\Services;

use App\Entity\Game;
use App\Entity\Player;
use App\Entity\Recap;
use App\enums\GameStatus;
use App\Exception\RecapExistingException;
use App\Exception\RecapNotFoundException;
use App\Repository\PlayerRepository;
use App\Repository\RecapRepository;
use App\Utils\FieldsHandler;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use App\Validation\Recap as ValidationRecap;
use DateTime;
use Symfony\Component\Serializer\SerializerInterface;


class RecapHandler
{
    public function __construct(private EntityManagerInterface $entity_manager, private RecapRepository $recap_repository, private Validation $validation, private SerializerInterface $serializer_interface, private PlayerRepository $player_repository, private GameHandler $game_handler, private PlayerHandler $player_handler) {}

    public function add(array $data)
    {
        $player = $this->findPlayer();

        $game = $this->createAndValidateGame($data);

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

    public function update(array $data)
    {
        $player = $this->findPlayer();

        $game = $this->createAndValidateGame($data);

        $existing_recap = $this->exists($player, $game);

        if (!$existing_recap) {
            throw new RecapNotFoundException("Ce récapitulatif n'existe pas, impossible de le mettre à jour.");
        }

        $game_status = FieldsHandler::gameStatus($data['gameStatus'] ?? null);

        $this->validateAndUpdate($existing_recap, $game_status, $data['rating']);

        $this->entity_manager->flush();
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
        $recap_data = [
            'player' => $player,
            'game' => $game,
            'status' => $game_status,
            'rating' => $rating
        ];

        $recap =  $this->validation->validate(ValidationRecap::class, $recap_data, ['groups' => ['recap:add']]);

        $recap->setPlayer($player);
        $recap->setGame($game);

        return $this->create($player, $game, $game_status, $rating);
    }

    private function validateAndUpdate(Recap $recap, GameStatus $status, ?int $rating): void
    {

        if ($rating !== $recap->getRating() || $status !== $recap->getStatus()) {

            $recap->setRating($rating);

            $recap->setStatus($status);

            $recap->setLastUpdated(new DateTime());

            $this->validation->validate(Recap::class, [
                'player' => $recap->getPlayer(),
                'game' => $recap->getGame(),
                'status' => $status,
                'rating' => $rating
            ]);
        }
    }

    private function verifyExistingRecap(Player $player, Game $game): void
    {
        $existing_recap = $this->exists($player, $game);

        if ($existing_recap) {
            throw new RecapExistingException('Ce jeu est déjà présent dans votre bibliothèque.');
        }
    }

    private function findPlayer(): Player
    {
        return $this->player_handler->getProfile();
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

    private function createAndValidateGame(array $game_data): Game
    {
        return $this->game_handler->validateAndCreate($game_data);
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

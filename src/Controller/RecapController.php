<?php

namespace App\Controller;

use App\Exception\GameExistsException;
use App\Exception\PlayerProfileNotFoundException;
use App\Exception\RecapExistingException;
use App\Exception\RecapNotFoundException;
use App\Exception\ValidationException;
use App\Services\PlayerHandler;
use App\Services\RecapHandler;
use App\Services\Validation;
use App\Utils\FormatingErrors;
use App\Utils\Json;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/recap', name: 'api.recap')]
final class RecapController extends AbstractController
{

    private $request;

    public function __construct(RequestStack $request_stack, private EntityManagerInterface $entity_manager, private Validation $validation, private SerializerInterface $serializer_interface, private RecapHandler $recap_handler, private NormalizerInterface $normalizer_interface, private PlayerHandler $player_handler)
    {
        $this->request = $request_stack->getCurrentRequest();
    }

    #[Route('/all', name: '.get_all', methods: ['GET'])]
    public function getRecaps(): JsonResponse
    {

        try {
            $player_profile = $this->player_handler->getProfile();
            $player_games = $this->recap_handler->getRecaps($player_profile);

            return Json::response([
                'success' => true,
                'games' => $this->normalizer_interface->normalize($player_games, null, ['groups' => ['recap:read']])
            ], JsonResponse::HTTP_OK);
        } catch (PlayerProfileNotFoundException | RecapNotFoundException $e) {

            return Json::response([
                'success' => false,
                'message' => "Une erreur est survenue : " . $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return Json::response([
                'success' => false,
                'message' => "Une erreur interne est survenue : " . $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/get/{api_game_id}', name: '.get_one', methods: ['GET'])]
    public function getOneRecap(string $api_game_id): JsonResponse
    {

        try {

            $recap = $this->recap_handler->getOneRecap($api_game_id);

            return Json::response([
                'success' => true,
                'recap' => json_decode($this->serializer_interface->serialize($recap, 'json', ['groups' => 'recap:read'])),
            ], JsonResponse::HTTP_OK);
        } catch (PlayerProfileNotFoundException | RecapNotFoundException $exception) {

            return Json::response([
                'success' => false,
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {

            return Json::response([
                'success' => false,
                'message' => 'Une erreur inattendue est survenue : ' . $exception->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/add', name: '.add', methods: ['POST'])]
    public function addRecap()
    {
        try {
            $data = Json::decode($this->request->getContent());
            $game = $this->recap_handler->add($data);

            return Json::response([
                'success' => true,
                'message' => "Le jeu a été ajouté avec succès.",
                'game' => json_decode($this->serializer_interface->serialize($game, 'json', ['groups' => 'game:read']))
            ], JsonResponse::HTTP_CREATED);
        } catch (ValidationException $e) {

            return Json::response([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getErrors() ? FormatingErrors::format($e->getErrors()) : null
            ], JsonResponse::HTTP_BAD_REQUEST);
        } catch (RecapExistingException | GameExistsException $e) {

            return Json::response([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_CONFLICT);
        } catch (\Throwable $e) {

            return Json::response([
                'success' => false,
                'message' => 'Une erreur interne est survenue : ' . $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/update/{api_game_id}', name: '.update', methods: ['PUT'])]
    public function updateRecap()
    {

        try {

            $data = Json::decode($this->request->getContent());

            $this->recap_handler->update($data);

            return Json::response([
                'success' => true,
                'message' => 'Le récapitulatif a été mis à jour avec succès.',
            ], JsonResponse::HTTP_OK);
        } catch (RecapNotFoundException $e) {

            return Json::response([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {

            return Json::response([
                'success' => false,
                'message' => "Les données fournies sont invalides.",
                'errors' => $e->getErrors() ? FormatingErrors::format($e->getErrors()) : null,
            ], JsonResponse::HTTP_BAD_REQUEST);
        } catch (RecapExistingException $e) {

            return Json::response([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_CONFLICT);
        } catch (\Throwable $e) {

            return Json::response([
                'success' => false,
                'message' => 'Une erreur interne est survenue : ' . $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/delete/{api_game_id}', name: '.delete', methods: ['DELETE'])]
    public function deleteRecap(string $api_game_id)
    {

        $recap = $this->recap_handler->getOneRecap($api_game_id);

        if (!$recap) {
            return Json::response(['success' => false, 'message' => 'Récapitulatif introuvable.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entity_manager->remove($recap);
        $this->entity_manager->remove($recap->getGame());
        $this->entity_manager->flush();

        return Json::response(['success' => true, 'message' => 'Récapitulatif supprimé avec succès.', 'recap' => $this->normalizer_interface->normalize($recap, null, ['groups' => ['recap:read']])]);
    }
}

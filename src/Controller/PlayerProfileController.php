<?php

namespace App\Controller;

use App\DTOs\ProfileDto;
use App\Exception\ValidationException;
use App\Services\PlayerHandler;
use App\Utils\Json;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/profile', name: 'api.profile')]
final class PlayerProfileController extends AbstractController
{

    public function __construct(private PlayerHandler $player_handler) {}

    #[Route('/get', name: '.get', methods: ['GET'])]
    public function get(): Response
    {
        $user = $this->getUser();
        $profile = new ProfileDto($user);

        return Json::response(['success' => true, 'player_profile' => $profile->getProfile()]);
    }

    #[Route('/update', name: '.update', methods: ['PUT'])]
    public function update(Request $request): Response
    {
        $data = Json::decode($request->getContent());
        try {

            $this->player_handler->update($data);

            return Json::response(['success' => true, 'message' => "Votre profil a bien été mis à jour.", "profile" => '']);
        } catch (FileException $e) {

            return Json::response([
                'success' => false,
                'error' => "L'ajout de l'avatar a échoué.",
                'message' => $e->getMessage(),
            ], Response::HTTP_NOT_ACCEPTABLE);
        } catch (ValidationException $e) {

            return Json::response([
                'success' => false,
                'error' => "Informations non conformes.",
                'message' => $e->getMessage(),
            ], Response::HTTP_NOT_ACCEPTABLE);
        } catch (Exception $e) {

            return Json::response([
                'success' => false,
                'error' => "Une erreur innatendue est survenue.",
                'message' => $e->getMessage(),
            ], Response::HTTP_NOT_ACCEPTABLE);
        }
    }
}

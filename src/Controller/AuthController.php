<?php

namespace App\Controller;

use App\Exception\ValidationException;
use App\Repository\UserRepository;
use App\Services\RegistrationHandler;
use App\Utils\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/auth', name: 'app')]
final class AuthController extends AbstractController
{
    private Request $request;
    public function __construct(private RequestStack $request_stack, private UserRepository $user_repository, private RegistrationHandler $registration_handler,)
    {
        $this->request = $this->request_stack->getCurrentRequest();
    }

    #[Route('/connexion', name: '.login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        return Json::response(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/inscription', name: '.register', methods: ['POST'])]
    public function register(): JsonResponse
    {
        try {
            $data = $this->request->request->all();
            $file = $this->request->files->get('avatar');

            $registration = $this->registration_handler->buidAndValidate($data, $file);

            $this->registration_handler->register($registration);

            return Json::response([
                'success' => true,
                'message' => "Inscription réussie."
            ], JsonResponse::HTTP_CREATED);
        } catch (ValidationException $e) {

            return Json::response(['success' => false, 'message' => $e->getMessage()], Response::HTTP_EXPECTATION_FAILED);
        } catch (\Throwable $e) {

            return Json::response([
                'success' => false,
                'message' => 'Une erreur interne est survenue : ' . $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

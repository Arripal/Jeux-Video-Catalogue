<?php

namespace App\Controller;

use App\Exception\ValidationException;
use App\Repository\UserRepository;
use App\Services\RegistrationHandler;
use App\Services\Validation;
use App\Utils\FormatingErrors;
use App\Utils\Json;
use App\Validation\Credentials;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;


#[Route('/auth', name: 'app')]
final class AuthController extends AbstractController
{
    private Request $request;
    public function __construct(private RequestStack $request_stack, private UserRepository $user_repository, private RegistrationHandler $registration_handler, private Validation $validation)
    {
        $this->request = $this->request_stack->getCurrentRequest();
    }

    #[Route('/connexion', name: '.login', methods: ['POST'])]
    public function login(Request $request, JWTTokenManagerInterface $jwttoken_manager, UserRepository $user_repository): JsonResponse
    {
        try {
            $login_data = json_decode($request->getContent(), true);

            $email = $login_data['email'] ?? null;
            $password = $login_data['password'] ?? null;

            $credentials = new Credentials($email, $password);

            $this->validation->validate($credentials);

            $user = $user_repository->findOneBy(['email' => $email]);

            if (!$user || !password_verify($password, $user->getPassword())) {
                return Json::response(['success' => false, 'message' => 'Identifiants invalides.', 'invalid_credentials' => true], 401);
            }

            $token = $jwttoken_manager->create($user);

            return Json::response(['success' => true, 'message' => 'Connexion réussie.', 'token' => $token], JsonResponse::HTTP_CREATED);
        } catch (ValidationException $e) {
            return Json::response([
                'success' => false,
                'message' => "La connexion a échouée, identifiants invalides.",
                'errors' => FormatingErrors::format($e->getErrors())
            ], JsonResponse::HTTP_EXPECTATION_FAILED);
        } catch (\Throwable $e) {
            return Json::response([
                'success' => false,
                'message' => "Une erreur interne est survenue."
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        } catch (UserNotFoundException $e) {
            return Json::response([
                'success' => false,
                'message' => $e->getMessage(),
                'user_not-found' => "L'adresse mail fournie ne correspond à aucun utilisateur."
            ], JsonResponse::HTTP_EXPECTATION_FAILED);
        }
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

            return Json::response(['success' => false, 'message' => $e->getMessage(), 'errors' => $e->getErrors() ? FormatingErrors::format($e->getErrors()) : null], Response::HTTP_EXPECTATION_FAILED);
        } catch (\Throwable $e) {

            return Json::response([
                'success' => false,
                'message' => 'Une erreur interne est survenue : ' . $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

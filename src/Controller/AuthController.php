<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\User;
use App\Exceptions\RegistrationException;
use App\Repository\UserRepository;
use App\Utils\FormatingErrors;
use App\Utils\Json;
use App\Validation\Registration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/auth', name: 'app')]
final class AuthController extends AbstractController
{

    public function __construct(private RequestStack $request_stack, private UserRepository $user_repository, private UserPasswordHasherInterface $password_hasher, private ValidatorInterface $validator_interface) {}

    #[Route('/connexion', name: '.login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        return Json::response(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/inscription', name: '.register', methods: ['POST'])]
    public function register(EntityManagerInterface $entity_manager): JsonResponse
    {

        $data = Json::decode($this->request_stack->getCurrentRequest()->getContent());

        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $username = $data['username'] ?? '';
        $bio = $data['bio'] ?? null;
        $avatar = $data['avatar'] ?? null;
        $location = $data['location'] ?? null;

        $registration = new Registration($email, $password, $username, $bio, $avatar, $location);
        $errors = $this->validator_interface->validate($registration);

        if (count($errors) > 0) {
            return Json::response(['success' => false, 'message' => "Impossible de vous inscrire.", 'errors' => FormatingErrors::format($errors)]);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->password_hasher->hashPassword($user, $password));

        $player_profile = new Player();
        $player_profile->setProfileUser($user);
        $player_profile->setUsername($data['username']);
        $player_profile->setBio($data['bio'] ?? null);
        $player_profile->setAvatar($data['avatar'] ?? null);
        $player_profile->setLocation($data['location'] ?? null);

        $entity_manager->persist($user);
        $entity_manager->persist($player_profile);
        $entity_manager->flush();

        return Json::response([
            'success' => true,
            'message' => "Inscription réussie."
        ], JsonResponse::HTTP_CREATED);
    }
}

<?php

namespace App\Security;

use App\Exception\ValidationException;
use App\Services\Validation;
use App\Validation\Credentials;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @see https://symfony.com/doc/current/security/custom_authenticator.html
 */
class LoginAuthenticator extends AbstractAuthenticator
{

    public function __construct(private ValidatorInterface $validator_interface, private UserProviderInterface $user_provider, private JWTTokenManagerInterface $jwttoken_manager, private Validation $validation) {}

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'app.login';
    }

    public function authenticate(Request $request): Passport
    {

        $login_data = json_decode($request->getContent(), true);

        $email = $login_data['email'] ?? null;
        $password = $login_data['password'] ?? null;

        $credentials = new Credentials($email, $password);

        $this->validation->validate($credentials);

        $user = $this->user_provider->loadUserByIdentifier($email);

        return new SelfValidatingPassport(
            new UserBadge($email, fn($email) => $user),
            [new PasswordCredentials($password)]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
        $jwt = $this->jwttoken_manager->create($user);

        $data = ['success' => true, 'message' => 'Vous êtes maintenant connecté.', 'token' => $jwt];

        return new JsonResponse($data, JsonResponse::HTTP_ACCEPTED);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = "Connexion impossible. Identifiants invalides.";

        return new JsonResponse([
            'success' => false,
            'message' => $message,
            'logged_in' => false
        ], Response::HTTP_UNAUTHORIZED);
    }
}

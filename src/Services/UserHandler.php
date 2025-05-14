<?php

namespace App\Services;

use App\Entity\User;
use App\Validation\Registration;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserHandler
{
    public function __construct(private UserPasswordHasherInterface $user_password_hasher) {}

    public function createUser(Registration $registration_data)
    {
        $user = new User();
        $user->setEmail($registration_data->getEmail())
            ->setPassword($this->user_password_hasher->hashPassword($user, $registration_data->getPassword()));

        return $user;
    }
}

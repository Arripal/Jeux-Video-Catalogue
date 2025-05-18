<?php

namespace App\Validation;

use Symfony\Component\Validator\Constraints as Assert;

class Credentials
{
    #[Assert\NotBlank(message: "L'email est requis.")]
    #[Assert\NotNull(message: "Un email est requis.")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas un email valide.")]
    private ?string $email;

    #[Assert\NotBlank(message: "Le mot de passe est requis.")]
    #[Assert\NotNull(message: "Un mot de passe est requis.")]
    #[Assert\Length(
        min: 6,
        max: 255,
        minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le mot de passe ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $password;

    public function __construct(?string $email,  ?string $password)
    {
        $this->email = $email !== null ? strip_tags(trim($email)) : null;
        $this->password = $password !== null ? strip_tags(trim($password)) : null;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}

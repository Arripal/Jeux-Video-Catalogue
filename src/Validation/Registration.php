<?php

namespace App\Validation;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validation\Constraints as CustomAssert;

class Registration
{

    #[Assert\NotBlank(message: "L'email est requis.")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas un email valide.")]
    #[CustomAssert\UniqueEmail]
    private string $email;

    #[Assert\NotBlank(message: "Le mot de passe est requis.")]
    #[Assert\Length(
        min: 6,
        max: 255,
        minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le mot de passe ne peut pas dépasser {{ limit }} caractères."
    )]
    private string $password;

    #[Assert\NotBlank(message: "Le pseudonyme est requis.")]
    private string $username;

    private ?string $bio;

    #[CustomAssert\ValidImage]
    private ?UploadedFile $avatar;

    private ?string $location;

    public function __construct(string $email, string $password, string $username, ?string $bio, ?UploadedFile $avatar, ?string $location)
    {
        $this->email = strip_tags(trim($email));
        $this->password = strip_tags(trim($password));
        $this->username = strip_tags(trim($username));
        $this->bio = strip_tags(trim($bio));
        $this->avatar = $avatar;
        $this->location = strip_tags(trim($location));
    }
}

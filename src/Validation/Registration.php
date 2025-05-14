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

    public function setEmail(string $email): void
    {
        $this->email = strip_tags(trim($email));
    }

    public function setPassword(string $password): void
    {
        $this->password = strip_tags(trim($password));
    }

    public function setUsername(string $username): void
    {
        $this->username = strip_tags(trim($username));
    }

    public function setBio(?string $bio): void
    {
        $this->bio = strip_tags(trim($bio));
    }

    public function setAvatar(?UploadedFile $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function setLocation(string $location): void
    {
        $this->location = strip_tags(trim($location));
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function getAvatar(): ?UploadedFile
    {
        return $this->avatar;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }
}

<?php

namespace App\Validation;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validation\Constraints as CustomAssert;

class Player
{

    #[Assert\NotBlank(message: "Le pseudonyme est requis.")]
    private string $username;

    private ?string $bio;

    #[CustomAssert\ValidImage]
    private ?UploadedFile $avatar;

    private ?string $location;

    public function __construct(string $username, ?string $bio, ?UploadedFile $avatar, ?string $location)
    {
        $this->username = strip_tags(trim($username));
        $this->bio = strip_tags(trim($bio));
        $this->avatar = $avatar;
        $this->location = strip_tags(trim($location));
    }

    public function getUsername(): ?string
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

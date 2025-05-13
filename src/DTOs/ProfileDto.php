<?php

namespace App\DTOs;

use App\Entity\Player;
use App\Entity\User;

class ProfileDto
{

    private ?User $user;
    private Player $player;
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->player = $this->user->getMyPlayerProfile();
    }

    public function getProfile()
    {
        return [
            'user' => [
                'email' => $this->user->getEmail(),
            ],
            'player' => [
                'username' => $this->player->getUsername(),
                'avatar' => $this->player->getAvatar(),
                'bio' => $this->player->getBio(),
                'location' => $this->player->getLocation(),
            ]
        ];
    }
}

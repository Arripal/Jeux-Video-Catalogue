<?php

namespace App\Services;

use App\Entity\Player;
use App\Exception\PlayerProfileNotFoundException;
use App\Validation\Player as ValidationPlayer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class PlayerHandler
{
    public function __construct(private Security $security, private Validation $validation, private EntityManagerInterface $entity_manager, private SluggerInterface $slugger, private UploadFile $upload_file) {}

    public function getProfile(): Player
    {
        $user = $this->security->getUser();

        if (!$user || !$user->getMyPlayerProfile()) {
            throw new PlayerProfileNotFoundException("Le profil joueur est introuvable.");
        }

        return $user->getMyPlayerProfile();
    }

    public function update(array $data)
    {
        $player_profile = $this->getProfile();
        $valid_player_profile = $this->validation->validate(ValidationPlayer::class, $data);

        $this->updatePlayerProfile($valid_player_profile, $player_profile);

        $this->entity_manager->flush();
    }

    private function updatePlayerProfile(ValidationPlayer $player, Player $existing_player)
    {
        $existing_player->setBio($player->getBio())
            ->setLocation($player->getLocation())
            ->setUsername($player->getUsername());

        $avatar = $player->getAvatar();

        if ($avatar) {
            $file_path = $this->upload_file->upload($avatar);
            $existing_player->setAvatar($file_path);
        }
    }
}

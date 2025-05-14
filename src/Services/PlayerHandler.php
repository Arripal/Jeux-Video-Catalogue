<?php

namespace App\Services;

use App\Entity\Player;
use App\Entity\User;
use App\Exception\PlayerProfileNotFoundException;
use App\Validation\Registration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PlayerHandler
{
    public function __construct(private Security $security, private Validation $validation, private EntityManagerInterface $entity_manager, private UploadFile $upload_file, private UserHandler $user_handler) {}

    public function getProfile(): Player
    {
        $user = $this->security->getUser();

        if (!$user || !$user->getMyPlayerProfile()) {
            throw new PlayerProfileNotFoundException("Le profil joueur est introuvable.");
        }

        return $user->getMyPlayerProfile();
    }

    public function update(array $data, ?UploadedFile $uploaded_file = null)
    {

        $this->updatePlayerProfile($data, $uploaded_file);

        $this->entity_manager->flush();
    }

    private function updatePlayerProfile(array $data, ?UploadedFile $file = null)
    {
        $player_profile = $this->getProfile();

        $bio = $data['bio'] ?? null;
        $location = $data['location'] ?? null;

        $player_profile
            ->setBio($bio)
            ->setLocation($location);

        if ($file instanceof UploadedFile) {

            if ($player_profile->getAvatar()) {
                $this->upload_file->remove($player_profile->getAvatar());
            }

            $avatar_path = $this->upload_file->upload($file);
            $player_profile->setAvatar($avatar_path);
        }

        $this->validation->validate($player_profile);
    }

    public function createPlayerProfile(Registration $registration_data, User $user)
    {
        $player_profile = new Player();
        $avatar = $this->upload_file->upload($registration_data->getAvatar());

        $player_profile->setProfileUser($user)
            ->setUsername($registration_data->getUsername())
            ->setBio($registration_data->getBio())
            ->setAvatar($avatar)
            ->setLocation($registration_data->getLocation());

        return $player_profile;
    }
}

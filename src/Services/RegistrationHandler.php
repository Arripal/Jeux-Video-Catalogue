<?php

namespace App\Services;

use App\Entity\Player;
use App\Entity\User;
use App\Validation\Registration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\SerializerInterface;

class RegistrationHandler
{

    public function __construct(private Validation $validation, private SerializerInterface $serializer, private EntityManagerInterface $entity_manager, private PlayerHandler $player_handler, private UserHandler $user_handler) {}

    public function buidAndValidate(array $data, ?UploadedFile $uploaded_file)
    {
        $registration = $this->serializer->deserialize(json_encode($data), Registration::class, 'json');

        if ($uploaded_file instanceof UploadedFile) {
            $registration->setAvatar($uploaded_file);
        }

        $this->validation->validate($registration);

        return $registration;
    }

    private function save(User $user, Player $player)
    {
        $this->entity_manager->persist($user);
        $this->entity_manager->persist($player);
        $this->entity_manager->flush();
    }

    public function register(Registration $registration)
    {
        $user = $this->user_handler->createUser($registration);

        $player_profile = $this->player_handler->createPlayerProfile($registration, $user);

        $this->save($user, $player_profile);
    }
}

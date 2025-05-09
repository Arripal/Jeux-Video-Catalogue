<?php

namespace App\Entity;

use App\Exception\RecapNotFoundException;
use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[UniqueEntity(
    fields: ['username'],
    message: "Ce pseudonyme n'est plus disponible."
)]
#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'myPlayerProfile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $profileUser = null;

    #[ORM\Column(length: 255, nullable: false, unique: true)]
    #[Groups(['recap:read'])]
    private ?string $username = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = null;

    /**
     * @var Collection<int, Recap>
     */
    #[ORM\OneToMany(targetEntity: Recap::class, mappedBy: 'player', orphanRemoval: true)]
    private Collection $recaps;

    public function __construct()
    {
        $this->recaps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfileUser(): ?User
    {
        return $this->profileUser;
    }

    public function setProfileUser(User $profileUser): static
    {
        $this->profileUser = $profileUser;

        return $this;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection<int, Recap>
     */
    public function getRecaps(): Collection
    {
        return $this->recaps;
    }

    public function addRecap(Recap $recap): static
    {
        if (!$this->recaps->contains($recap)) {
            $this->recaps->add($recap);
            $recap->setPlayer($this);
        }

        return $this;
    }

    public function removeRecap(Recap $recap): static
    {
        if ($this->recaps->removeElement($recap)) {
            // set the owning side to null (unless already changed)
            if ($recap->getPlayer() === $this) {
                $recap->setPlayer(null);
            }
        }

        return $this;
    }

    public function getOneRecap(string $api_game_id): Recap
    {
        $recap = $this->recaps->filter(function (Recap $recap) use ($api_game_id) {
            return $recap->getGame()->getApiID() === $api_game_id;
        })->first();

        if (!$recap) {
            throw new RecapNotFoundException("Le récapitulatif est introuvable.");
        }

        return $recap;
    }
}

<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['recap:read', 'game:read'])]
    #[Assert\NotNull(message: "L'identifiant du jeu est obligatoire.")]
    private ?string $apiID = null;

    #[ORM\Column(length: 255)]
    #[Groups(['recap:read', 'game:read'])]
    #[Assert\NotBlank(message: "Un titre valide est nécessaire.")]
    #[Assert\NotNull(message: "Le titre est obligatoire.")]
    private ?string $title = null;

    #[ORM\Column]
    #[Groups(['recap:read', 'game:read'])]
    #[Assert\NotNull(message: "La date de sortie est obligatoire.")]
    private ?\DateTimeImmutable $releaseDate = null;

    #[Groups(['game:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $publisher = null;

    #[ORM\Column(nullable: false)]
    #[Groups(['recap:read', 'game:read'])]
    #[Assert\Type('array', message: "Les genres doivent être dans un tableau.")]
    #[Assert\NotNull(message: "Genres obligatoire.")]
    #[Assert\Count(min: 1, minMessage: "Au moins un genre est requis.")]
    private array $genres = [];

    #[ORM\Column(nullable: false)]
    #[Groups(['recap:read', 'game:read'])]
    #[Assert\Type('array', message: "Les plateformes doivent être dans un tableau.")]
    #[Assert\NotNull(message: "Les plateformes sont obligatoires.")]
    #[Assert\Count(min: 1, minMessage: "Au moins une plateforme est requise.")]
    private array $plateforms = [];

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['recap:read', 'game:read'])]
    private ?string $franchise = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['recap:read', 'game:read'])]
    private ?string $developers = null;

    #[ORM\Column(nullable: true)]
    private ?int $globalRating = null;

    #[ORM\Column(nullable: true)]
    private ?int $ratingCount = null;

    /**
     * @var Collection<int, Recap>
     */
    #[ORM\OneToMany(targetEntity: Recap::class, mappedBy: 'game', orphanRemoval: true)]
    private Collection $recaps;

    public function __construct()
    {
        $this->recaps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApiID(): ?string
    {
        return $this->apiID;
    }

    public function setApiID(string $apiID): static
    {
        $this->apiID = $apiID;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeImmutable
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTimeImmutable $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(?string $publisher): static
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function getGenres(): array
    {
        return $this->genres;
    }

    public function setGenres(array $genres): static
    {
        $this->genres = $genres;

        return $this;
    }

    public function getPlateforms(): array
    {
        return $this->plateforms;
    }

    public function setPlateforms(array $plateforms): static
    {
        $this->plateforms = $plateforms;

        return $this;
    }

    public function getFranchise(): ?string
    {
        return $this->franchise;
    }

    public function setFranchise(?string $franchise): static
    {
        $this->franchise = $franchise;

        return $this;
    }

    public function getDevelopers(): ?string
    {
        return $this->developers;
    }

    public function setDevelopers(?string $developers): static
    {
        $this->developers = $developers;

        return $this;
    }

    public function getGlobalRating(): ?int
    {
        return $this->globalRating;
    }

    public function setGlobalRating(?int $globalRating): static
    {
        $this->globalRating = $globalRating;

        return $this;
    }

    public function getRatingCount(): ?int
    {
        return $this->ratingCount;
    }

    public function setRatingCount(?int $ratingCount): static
    {
        $this->ratingCount = $ratingCount;

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
            $recap->setGame($this);
        }

        return $this;
    }

    public function removeRecap(Recap $recap): static
    {
        if ($this->recaps->removeElement($recap)) {
            // set the owning side to null (unless already changed)
            if ($recap->getGame() === $this) {
                $recap->setGame(null);
            }
        }

        return $this;
    }
}

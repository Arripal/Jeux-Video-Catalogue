<?php

namespace App\Validation;

use Symfony\Component\Validator\Constraints as Assert;

class Game
{
    #[Assert\NotNull(message: "L'identifiant du jeu est obligatoire.")]
    private string $apiID;

    private ?string $developers = null;

    #[Assert\Type('array', message: "Les genres doivent être dans un tableau.")]
    #[Assert\NotNull(message: "Genres obligatoire.")]
    private ?array $genres = null;

    private ?string $franchise = null;

    private ?int $globalRating = null;

    #[Assert\Type('array', message: "Les plateformes doivent être dans un tableau.")]
    #[Assert\NotNull(message: "Les plateformes sont obligatoire.")]
    private ?array $plateforms = null;

    private ?string $publisher = null;

    private ?int $ratingCount = null;

    #[Assert\NotNull(message: "La date de sortie est obligatoire.")]
    private ?\DateTimeImmutable $releaseDate = null;

    #[Assert\NotBlank(message: "Un titre valide est nécessaire.")]
    #[Assert\NotNull(message: "Le titre est obligatoire, il ne peut pas être null.")]
    private ?string $title = null;

    public function __construct(
        string $apiID,
        ?string $developers = null,
        ?array $genres = null,
        ?string $franchise = null,
        ?int $globalRating = null,
        ?array $plateforms = null,
        ?string $publisher = null,
        ?int $ratingCount = null,
        ?\DateTimeImmutable $releaseDate = null,
        ?string $title = null
    ) {
        $this->apiID = $apiID;
        $this->developers = $developers;
        $this->genres = $genres;
        $this->franchise = $franchise;
        $this->globalRating = $globalRating;
        $this->plateforms = $plateforms;
        $this->publisher = $publisher;
        $this->ratingCount = $ratingCount;
        $this->releaseDate = $releaseDate;
        $this->title = $title;
    }

    public function getApiId()
    {
        return $this->apiID;
    }

    public function getDevelopers()
    {
        return $this->developers;
    }

    public function getGenres()
    {
        return $this->genres;
    }

    public function getFranchise()
    {
        return $this->franchise;
    }

    public function getGlobalRating()
    {
        return $this->globalRating;
    }

    public function getPlateforms()
    {
        return $this->plateforms;
    }

    public function getPublisher()
    {
        return $this->publisher;
    }

    public function getRatingCount()
    {
        return $this->ratingCount;
    }

    public function getReleaseDate()
    {
        return $this->releaseDate;
    }

    public function getTitle()
    {
        return $this->title;
    }
}

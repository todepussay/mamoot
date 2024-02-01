<?php

namespace App\Entity;

use App\Repository\QuizHistoriqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuizHistoriqueRepository::class)]
class QuizHistorique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy : "historiques")]
    #[ORM\JoinColumn(nullable : false)]
    private ?Quiz $quiz = null;

    #[ORM\Column(type: "datetime")]
    private $createdDate;

    #[ORM\OneToMany(mappedBy : "quiz", targetEntity : PlayerHistorique::class, cascade: ["persist", "remove"])]
    private Collection $players;

    public function __construct()
    {
        $this->players = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): void
    {
        $this->quiz = $quiz;
    }

    /**
     * @return mixed
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @param mixed $createdDate
     */
    public function setCreatedDate($createdDate): void
    {
        $this->createdDate = $createdDate;
    }

    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function setPlayers(Collection $players): void
    {
        $this->players = $players;
    }

    public function addPlayer(PlayerHistorique $player): void
    {
        $this->getPlayers()->add($player);
    }
}

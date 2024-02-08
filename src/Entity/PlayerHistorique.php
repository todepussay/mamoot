<?php

namespace App\Entity;

use App\Repository\PlayerHistoriqueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerHistoriqueRepository::class)]
class PlayerHistorique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ["persist", "remove"], inversedBy: "players")]
    #[ORM\JoinColumn(nullable: false)]
    private ?QuizHistorique $quiz = null;

    #[ORM\Column(type: "string", length: "255")]
    private $username;

    #[ORM\Column(type: "integer")]
    private $score;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuiz(): ?QuizHistorique
    {
        return $this->quiz;
    }

    public function setQuiz(?QuizHistorique $quiz): void
    {
        $this->quiz = $quiz;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param mixed $score
     */
    public function setScore($score): void
    {
        $this->score = $score;
    }


}

<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private $title;

    #[ORM\OneToMany(mappedBy : "quiz", targetEntity : Question::class, cascade: ["persist"])]
    private Collection $questions;

    #[ORM\OneToMany(mappedBy : "quiz", targetEntity : QuizHistorique::class, cascade: ["persist", "remove"])]
    private Collection $historiques;

    #[ORM\ManyToOne(targetEntity: "User", inversedBy: "quiz")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private $user;

    #[ORM\Column(type: "datetime")]
    private $createdDate;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function setQuestions(Collection $questions): void
    {
        $this->questions = $questions;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }



}

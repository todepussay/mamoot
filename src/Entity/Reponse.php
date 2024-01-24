<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private $reponse;

    #[ORM\Column(type: "boolean")]
    private $good;

    #[ORM\ManyToOne(cascade: ["persist"], inversedBy: "reponses")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Question $question = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getReponse()
    {
        return $this->reponse;
    }

    /**
     * @param mixed $reponse
     */
    public function setReponse($reponse): void
    {
        $this->reponse = $reponse;
    }

    /**
     * @return mixed
     */
    public function getGood()
    {
        return $this->good;
    }

    /**
     * @param mixed $good
     */
    public function setGood($good): void
    {
        $this->good = $good;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): void
    {
        $this->question = $question;
    }


}

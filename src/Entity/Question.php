<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private $label;

    #[ORM\Column(type: "boolean")]
    private $vraifaux;

    #[ORM\Column(type: "boolean")]
    private $valeurvraifaux;

    #[ORM\Column(type: "boolean")]
    private $curseur;

    #[ORM\Column(type: "integer")]
    private $temps;

    #[ORM\Column(type: "integer", nullable: true)]
    private $minimum_curseur;

    #[ORM\Column(type: "integer", nullable: true)]
    private $maximum_curseur;

    #[ORM\Column(type: "integer", nullable: true)]
    private $interval_curseur;

    #[ORM\Column(type: "integer", nullable: true)]
    private $minimum_valide;

    #[ORM\Column(type: "integer", nullable: true)]
    private $maximum_valide;

    #[ORM\ManyToOne(inversedBy : "questions")]
    #[ORM\JoinColumn(nullable : false)]
    private ?Quiz $quiz = null;

    #[ORM\OneToMany(mappedBy : "question", targetEntity : Reponse::class, cascade: ["persist"])]
    private Collection $reponses;

    /**
     * @param Collection $reponses
     */
    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label): void
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getVraifaux()
    {
        return $this->vraifaux;
    }

    /**
     * @param mixed $vraifaux
     */
    public function setVraifaux($vraifaux): void
    {
        $this->vraifaux = $vraifaux;
    }

    /**
     * @return mixed
     */
    public function getValeurvraifaux()
    {
        return $this->valeurvraifaux;
    }

    /**
     * @param mixed $valeurvraifaux
     */
    public function setValeurvraifaux($valeurvraifaux): void
    {
        $this->valeurvraifaux = $valeurvraifaux;
    }

    /**
     * @return mixed
     */
    public function getCurseur()
    {
        return $this->curseur;
    }

    /**
     * @param mixed $curseur
     */
    public function setCurseur($curseur): void
    {
        $this->curseur = $curseur;
    }

    /**
     * @return mixed
     */
    public function getTemps()
    {
        return $this->temps;
    }

    /**
     * @param mixed $temps
     */
    public function setTemps($temps): void
    {
        $this->temps = $temps;
    }

    /**
     * @return mixed
     */
    public function getMinimumCurseur()
    {
        return $this->minimum_curseur;
    }

    /**
     * @param mixed $minimum_curseur
     */
    public function setMinimumCurseur($minimum_curseur): void
    {
        $this->minimum_curseur = $minimum_curseur;
    }

    /**
     * @return mixed
     */
    public function getMaximumCurseur()
    {
        return $this->maximum_curseur;
    }

    /**
     * @param mixed $maximum_curseur
     */
    public function setMaximumCurseur($maximum_curseur): void
    {
        $this->maximum_curseur = $maximum_curseur;
    }

    /**
     * @return mixed
     */
    public function getIntervalCurseur()
    {
        return $this->interval_curseur;
    }

    /**
     * @param mixed $interval_curseur
     */
    public function setIntervalCurseur($interval_curseur): void
    {
        $this->interval_curseur = $interval_curseur;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): void
    {
        $this->quiz = $quiz;
    }

    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function setReponses(Collection $reponses): void
    {
        $this->reponses = $reponses;
    }

    public function addReponse(Reponse $reponse): void
    {
        $this->reponses->add($reponse);
    }

    public function removeReponse(): void
    {
        $this->reponses->removeElement($this->reponses->last());
    }

    /**
     * @return mixed
     */
    public function getMinimumValide()
    {
        return $this->minimum_valide;
    }

    /**
     * @param mixed $minimum_valide
     */
    public function setMinimumValide($minimum_valide): void
    {
        $this->minimum_valide = $minimum_valide;
    }

    /**
     * @return mixed
     */
    public function getMaximumValide()
    {
        return $this->maximum_valide;
    }

    /**
     * @param mixed $maximum_valide
     */
    public function setMaximumValide($maximum_valide): void
    {
        $this->maximum_valide = $maximum_valide;
    }



    public function __toString(): string
    {
        return "Question : \n-Label : " . $this->label . "\n-Vrai faux : " . $this->vraifaux . "\n-Curseur : " .
            $this->curseur . "\n-Temps : " . $this->temps . "\n-Maximum Curseur : " .
            $this->maximum_curseur . "\n-Minimum Curseur : " . $this->minimum_curseur .
            "\n-Interval : " . $this->interval_curseur;
    }


}

<?php

namespace App\Entity;

use App\Repository\UERepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UERepository::class)]
#[UniqueEntity(fields: ['name'], message: 'This UE already exists')]
class UE
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 15)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 1,
        max: 200,
        notInRangeMessage: 'You must be between {{ min }} and {{ max }} students',
    )]
    private ?int $nbStudent = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 1,
        max: 200,
        notInRangeMessage: 'You must be between {{ min }} and {{ max }} groups',
    )]
    private ?int $nbGroup = null;

    #[ORM\Column(nullable: true)]
    private ?bool $constraintsApplied = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 1,
        max: 2,
        notInRangeMessage: 'Semester must be 1 or 2',
    )]
    private ?int $semester = null;

    #[ORM\OneToMany(mappedBy: 'uE', targetEntity: Cours::class, orphanRemoval: true)]
    private Collection $cours;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $teacher = null;

    #[ORM\Column()]
    #[Assert\Range(
        min: 0,
        max: 200,
        notInRangeMessage: 'nbCours must be between {{ min }} and {{ max }}',
    )]
    private ?int $nbCours = null;

    #[ORM\Column()]
    #[Assert\Range(
        min: 0,
        max: 200,
        notInRangeMessage: 'nbTD must be between {{ min }} and {{ max }}',
    )]
    private ?int $nbTD = null;

    #[ORM\Column()]
    #[Assert\Range(
        min: 0,
        max: 200,
        notInRangeMessage: 'nbTP must be between {{ min }} and {{ max }}',
    )]
    private ?int $nbTP = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $filieres = [];

    public function __construct()
    {
        $this->cours = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getNbStudent(): ?int
    {
        return $this->nbStudent;
    }

    public function setNbStudent(int $nbStudent): self
    {
        $this->nbStudent = $nbStudent;

        return $this;
    }

    public function getNbGroup(): ?int
    {
        return $this->nbGroup;
    }

    public function setNbGroup(int $nbGroup): self
    {
        $this->nbGroup = $nbGroup;

        return $this;
    }

    public function isConstraintsApplied(): ?bool
    {
        return $this->constraintsApplied;
    }

    public function setConstraintsApplied(bool $constraintsApplied = false): self
    {
        $this->constraintsApplied = $constraintsApplied;

        return $this;
    }

    public function getSemester(): ?int
    {
        return $this->semester;
    }

    public function setSemester(int $semester): self
    {
        if ($semester == 1 || $semester == 2) {
            $this->semester = $semester;
        }
        return $this;
    }

    /**
     * @return Collection<int, Cours>
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function cleanCours(): void
    {
        $this->cours = new ArrayCollection();
    }


    public function addCour(Cours $cour): bool
    {
        if (!$this->cours->contains($cour)) {
            $type = $cour->getType();
            $nbType = 0;
            foreach ($this->cours as $cours) {
                if ($cours->getType() == $type) {
                    $nbType++;
                }
            }
            $canAdd = true;
            // if we already have the max number of this type of course
            if ($type == 'Cours' && $nbType >= $this->nbCours) {
                $canAdd = false;
            } elseif ($type == 'TD' && $nbType >= $this->nbTD) {
                $canAdd = false;
            } elseif ($type == 'TP' && $nbType >= $this->nbTP) {
                $canAdd = false;
            }
            if ($canAdd) {
                $this->cours[] = $cour;
                $cour->setUE($this);
            }
            
            return $canAdd;
        }

        return false;
    }

    public function removeCour(Cours $cour): self
    {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getUE() === $this) {
                $cour->setUE(null);
            }
        }

        return $this;
    }

    public function getTeacher(): ?string
    {
        return $this->teacher;
    }

    public function setTeacher(string $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getNbCours(): ?int
    {
        return $this->nbCours;
    }

    public function setNbCours(?int $nbCours = 0): self
    {
        $this->nbCours = $nbCours;

        return $this;
    }

    public function getNbTD(): ?int
    {
        return $this->nbTD;
    }

    public function setNbTD(?int $nbTD = 0): self
    {
        $this->nbTD = $nbTD;

        return $this;
    }

    public function getNbTP(): ?int
    {
        return $this->nbTP;
    }

    public function setNbTP(?int $nbTP = 0): self
    {
        $this->nbTP = $nbTP;

        return $this;
    }

    public function getFilieres(): array
    {
        return $this->filieres;
    }

    public function setFilieres(array $filieres): self
    {
        $this->filieres = $filieres;

        return $this;
    }

    public function getNumberOfCourse(): int
    {
        return $this->nbCours + $this->nbTD + $this->nbTP;
    }

}

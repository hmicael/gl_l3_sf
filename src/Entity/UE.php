<?php

namespace App\Entity;

use App\Repository\UERepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UERepository::class)]
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
    private ?int $nbStudent = null;

    #[ORM\Column]
    private ?int $nbGroup = null;

    #[ORM\Column]
    private ?bool $constraintsApplied = null;

    #[ORM\Column]
    private ?int $semester = null;

    #[ORM\OneToMany(mappedBy: 'uE', targetEntity: Cours::class)]
    private Collection $cours;

    #[ORM\Column(type: Types::ARRAY)]
    private array $filiere = [];

    #[ORM\Column(length: 30)]
    private ?string $teacher = null;

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
        $this->semester = $semester;

        return $this;
    }

    /**
     * @return Collection<int, Cours>
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(Cours $cour): self
    {
        if (!$this->cours->contains($cour)) {
            $this->cours->add($cour);
            $cour->setUE($this);
        }

        return $this;
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

    public function getFiliere(): array
    {
        return $this->filiere;
    }

    public function setFiliere(array $filiere): self
    {
        $this->filiere = $filiere;

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
}

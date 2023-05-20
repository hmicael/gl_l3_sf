<?php

namespace App\Entity;

use App\Repository\GeneralConstraintsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GeneralConstraintsRepository::class)]
class GeneralConstraints
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $startHour = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $endHour = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $breakDuration = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\Expression(
        expression: 'this.getCourseMinDuration() < this.getCourseMaxDuration()',
        message: 'The course min duration must be lower than the course max duration',
    )]
    private ?\DateTimeInterface $courseMaxDuration = null;

    #[ORM\OneToMany(mappedBy: 'generalConstraints', targetEntity: Holiday::class, orphanRemoval: true)]
    private Collection $holidays;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $courseMinDuration = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\Expression(
        expression: 'this.getSemesterOneStart() < this.getSemesterOneEnd()',
        message: 'The semester one start date must be before the semester one end date',
    )]
    private ?\DateTimeInterface $semesterOneStart = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $semesterOneEnd = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\Expression(
        expression: 'this.getSemesterTwoStart() < this.getSemesterTwoEnd()',
        message: 'The semester two start date must be before the semester two end date',
    )]
    private ?\DateTimeInterface $semesterTwoStart = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $semesterTwoEnd = null;

    public function __construct()
    {
        $this->holidays = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartHour(): ?\DateTimeInterface
    {
        return $this->startHour;
    }

    public function setStartHour(\DateTimeInterface $startHour): self
    {
        $this->startHour = $startHour;

        return $this;
    }

    public function getEndHour(): ?\DateTimeInterface
    {
        return $this->endHour;
    }

    public function setEndHour(\DateTimeInterface $endHour): self
    {
        $this->endHour = $endHour;

        return $this;
    }

    public function getBreakDuration(): ?\DateTimeInterface
    {
        return $this->breakDuration;
    }

    public function setBreakDuration(\DateTimeInterface $breakDuration): self
    {
        $this->breakDuration = $breakDuration;

        return $this;
    }

    public function getCourseMaxDuration(): ?\DateTimeInterface
    {
        return $this->courseMaxDuration;
    }

    public function setCourseMaxDuration(\DateTimeInterface $courseMaxDuration): self
    {
        $this->courseMaxDuration = $courseMaxDuration;

        return $this;
    }

    /**
     * @return Collection<int, Holiday>
     */
    public function getHolidays(): Collection
    {
        return $this->holidays;
    }

    public function addHoliday(Holiday $holiday): self
    {
        if (!$this->holidays->contains($holiday)) {
            $this->holidays->add($holiday);
            $holiday->setGeneralConstraints($this);
        }

        return $this;
    }

    public function removeHoliday(Holiday $holiday): self
    {
        if ($this->holidays->removeElement($holiday)) {
            // set the owning side to null (unless already changed)
            if ($holiday->getGeneralConstraints() === $this) {
                $holiday->setGeneralConstraints(null);
            }
        }

        return $this;
    }

    public function getCourseMinDuration(): ?\DateTimeInterface
    {
        return $this->courseMinDuration;
    }

    public function setCourseMinDuration(?\DateTimeInterface $courseMinDuration): self
    {
        $this->courseMinDuration = $courseMinDuration;

        return $this;
    }

    public function getSemesterOneStart(): ?\DateTimeInterface
    {
        return $this->semesterOneStart;
    }

    public function setSemesterOneStart(?\DateTimeInterface $semesterOneStart): self
    {
        $this->semesterOneStart = $semesterOneStart;

        return $this;
    }

    public function getSemesterOneEnd(): ?\DateTimeInterface
    {
        return $this->semesterOneEnd;
    }

    public function setSemesterOneEnd(?\DateTimeInterface $semesterOneEnd): self
    {
        $this->semesterOneEnd = $semesterOneEnd;

        return $this;
    }

    public function getSemesterTwoStart(): ?\DateTimeInterface
    {
        return $this->semesterTwoStart;
    }

    public function setSemesterTwoStart(?\DateTimeInterface $semesterTwoStart): self
    {
        $this->semesterTwoStart = $semesterTwoStart;

        return $this;
    }

    public function getSemesterTwoEnd(): ?\DateTimeInterface
    {
        return $this->semesterTwoEnd;
    }

    public function setSemesterTwoEnd(?\DateTimeInterface $semesterTwoEnd): self
    {
        $this->semesterTwoEnd = $semesterTwoEnd;

        return $this;
    }
}

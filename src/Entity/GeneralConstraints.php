<?php

namespace App\Entity;

use App\Repository\GeneralConstraintsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

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
    private ?\DateTimeInterface $courseMaxDuration = null;

    #[ORM\OneToMany(mappedBy: 'generalConstraints', targetEntity: Holiday::class, orphanRemoval: true)]
    private Collection $holidays;

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
}

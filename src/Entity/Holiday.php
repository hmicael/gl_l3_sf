<?php

namespace App\Entity;

use App\Repository\HolidayRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HolidayRepository::class)]
class Holiday
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 15)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\Expression(
        expression: 'this.getBeginning() < this.getEnd()',
        message: 'The beginning date must be before the end date',
    )]
    private ?\DateTimeInterface $beginning = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $end = null;

    #[ORM\ManyToOne(inversedBy: 'holidays')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GeneralConstraints $generalConstraints = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 1,
        max: 2,
        notInRangeMessage: 'Semester must be 1 or 2',
    )]
    private ?int $semester = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getBeginning(): ?\DateTimeInterface
    {
        return $this->beginning;
    }

    public function setBeginning(\DateTimeInterface $beginning): self
    {
        $this->beginning = $beginning;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(?\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getGeneralConstraints(): ?GeneralConstraints
    {
        return $this->generalConstraints;
    }

    public function setGeneralConstraints(?GeneralConstraints $generalConstraints): self
    {
        $this->generalConstraints = $generalConstraints;

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
}

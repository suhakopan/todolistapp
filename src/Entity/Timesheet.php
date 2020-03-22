<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TimesheetRepository")
 */
class Timesheet
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $TaskName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $DevName;

    /**
     * @ORM\Column(type="integer")
     */
    private $Duration;

    /**
     * @ORM\Column(type="integer")
     */
    private $Week;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaskName(): ?string
    {
        return $this->TaskName;
    }

    public function setTaskName(string $TaskName): self
    {
        $this->TaskName = $TaskName;

        return $this;
    }

    public function getDevName(): ?string
    {
        return $this->DevName;
    }

    public function setDevName(string $DevName): self
    {
        $this->DevName = $DevName;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->Duration;
    }

    public function setDuration(int $Duration): self
    {
        $this->Duration = $Duration;

        return $this;
    }

    public function getWeek(): ?int
    {
        return $this->Week;
    }

    public function setWeek(int $Week): self
    {
        $this->Week = $Week;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\PlanqueRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlanqueRepository::class)
 */
class Planque
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @ORM\ManyToOne(targetEntity=typeplanque::class, inversedBy="planques")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type_planque;

    /**
     * @ORM\ManyToOne(targetEntity=Mission::class, inversedBy="planques")
     * @ORM\JoinColumn(nullable=true)
     */
    private $mission;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTypePlanque(): ?typeplanque
    {
        return $this->type_planque;
    }

    public function setTypePlanque(?typeplanque $type_planque): self
    {
        $this->type_planque = $type_planque;

        return $this;
    }

    public function getMission(): ?Mission
    {
        return $this->mission;
    }

    public function setMission(?Mission $mission): self
    {
        $this->mission = $mission;

        return $this;
    }
}

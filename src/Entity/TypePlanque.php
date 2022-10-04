<?php

namespace App\Entity;

use App\Repository\TypePlanqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypePlanqueRepository::class)
 */
class TypePlanque
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
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=Planque::class, mappedBy="type_planque")
     */
    private $planques;

    public function __construct()
    {
        $this->planques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Planque>
     */
    public function getPlanques(): Collection
    {
        return $this->planques;
    }

    public function addPlanque(Planque $planque): self
    {
        if (!$this->planques->contains($planque)) {
            $this->planques[] = $planque;
            $planque->setTypePlanque($this);
        }

        return $this;
    }

    public function removePlanque(Planque $planque): self
    {
        if ($this->planques->removeElement($planque)) {
            // set the owning side to null (unless already changed)
            if ($planque->getTypePlanque() === $this) {
                $planque->setTypePlanque(null);
            }
        }

        return $this;
    }
}

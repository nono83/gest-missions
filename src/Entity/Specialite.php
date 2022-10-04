<?php

namespace App\Entity;

use App\Repository\SpecialiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SpecialiteRepository::class)
 */
class Specialite
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
     * @ORM\ManyToMany(targetEntity=Agent::class, mappedBy="specialite")
     */
    private $agents;

    /**
     * @ORM\OneToMany(targetEntity=AgentSpecialite::class, mappedBy="specialite")
     */
    private $agentSpecialites;

    /**
     * @ORM\OneToMany(targetEntity=Mission::class, mappedBy="specialite")
     */
    private $missions;

    public function __construct()
    {
        $this->agents = new ArrayCollection();
        $this->agentSpecialites = new ArrayCollection();
        $this->missions = new ArrayCollection();
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
     * @return Collection<int, AgentSpecialite>
     */
    public function getAgentSpecialites(): Collection
    {
        return $this->agentSpecialites;
    }

    public function addAgentSpecialite(AgentSpecialite $agentSpecialite): self
    {
        if (!$this->agentSpecialites->contains($agentSpecialite)) {
            $this->agentSpecialites[] = $agentSpecialite;
            $agentSpecialite->setSpecialite($this);
        }

        return $this;
    }

    public function removeAgentSpecialite(AgentSpecialite $agentSpecialite): self
    {
        if ($this->agentSpecialites->removeElement($agentSpecialite)) {
            // set the owning side to null (unless already changed)
            if ($agentSpecialite->getSpecialite() === $this) {
                $agentSpecialite->setSpecialite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Mission>
     */
    public function getMissions(): Collection
    {
        return $this->missions;
    }

    public function addMission(Mission $mission): self
    {
        if (!$this->missions->contains($mission)) {
            $this->missions[] = $mission;
            $mission->setSpecialite($this);
        }

        return $this;
    }

    public function removeMission(Mission $mission): self
    {
        if ($this->missions->removeElement($mission)) {
            // set the owning side to null (unless already changed)
            if ($mission->getSpecialite() === $this) {
                $mission->setSpecialite(null);
            }
        }

        return $this;
    }

    
}

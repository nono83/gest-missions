<?php

namespace App\Entity;

// Import avec un alias afin de réduire la verbosité de nos validations
use Symfony\Component\Validator\Constraints as Assert;
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
     * @Assert\Length(
     *     max = 50,
     *     maxMessage = "Ce titre est trop long"
     * )
     * @Assert\NotBlank(message = "Le nom ne peut être vide.")
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=Mission::class, mappedBy="specialite")
     */
    private $missions;

    /**
     * @ORM\ManyToMany(targetEntity=Agent::class, mappedBy="specialites")
     */
    private $agents;

    public function __construct()
    {
        $this->missions = new ArrayCollection();
        $this->agents = new ArrayCollection();
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

    /**
     * @return Collection<int, Agent>
     */
    public function getAgents(): Collection
    {
        return $this->agents;
    }

    public function addAgent(Agent $agent): self
    {
        if (!$this->agents->contains($agent)) {
            $this->agents[] = $agent;
            $agent->addSpecialite($this);
        }

        return $this;
    }

    public function removeAgent(Agent $agent): self
    {
        if ($this->agents->removeElement($agent)) {
            $agent->removeSpecialite($this);
        }

        return $this;
    }
    
}

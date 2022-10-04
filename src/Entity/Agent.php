<?php

namespace App\Entity;

use App\Repository\AgentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AgentRepository::class)
 */
class Agent
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
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $prenom;

    /**
     * @ORM\Column(type="date")
     */
    private $date_naissance;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $code_identification;

    /**
     * @ORM\ManyToOne(targetEntity=Pays::class, inversedBy="agents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $nationalite;

    /**
     * @ORM\ManyToOne(targetEntity=Mission::class, inversedBy="agents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mission;

    /**
     * @ORM\OneToMany(targetEntity=AgentSpecialite::class, mappedBy="agent")
     */
    private $agentSpecialites;

    public function __construct()
    {
        $this->specialite = new ArrayCollection();
        $this->agentSpecialites = new ArrayCollection();
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(\DateTimeInterface $date_naissance): self
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }

    public function getCodeIdentification(): ?string
    {
        return $this->code_identification;
    }

    public function setCodeIdentification(string $code_identification): self
    {
        $this->code_identification = $code_identification;

        return $this;
    }

    public function getNationalite(): ?Pays
    {
        return $this->nationalite;
    }

    public function setNationalite(?Pays $nationalite): self
    {
        $this->nationalite = $nationalite;

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
            $agentSpecialite->setAgent($this);
        }

        return $this;
    }

    public function removeAgentSpecialite(AgentSpecialite $agentSpecialite): self
    {
        if ($this->agentSpecialites->removeElement($agentSpecialite)) {
            // set the owning side to null (unless already changed)
            if ($agentSpecialite->getAgent() === $this) {
                $agentSpecialite->setAgent(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

// Import avec un alias afin de réduire la verbosité de nos validations
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\MissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MissionRepository::class)
 */
class Mission
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\Length(
     *     max = 150,
     *     maxMessage = "Ce titre est trop long"
     * )
     * @Assert\NotBlank(message = "Le titre ne peut être vide.")
     */
    private $titre;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message = "La description ne peut être vide.")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\Length(
     *     max = 100,
     *     maxMessage = "Ce nom de code est trop long"
     * )
     * @Assert\NotBlank(message = "Le nom de code ne peut être vide.")
     */
    private $nom_code;

    /**
     * @ORM\Column(type="date")
     */
    private $date_debut;

    /**
     * @ORM\Column(type="date")
     */
    private $date_fin;

    /**
     * @ORM\OneToMany(targetEntity=Planque::class, mappedBy="mission")
     */
    private $planques;

    /**
     * @ORM\OneToMany(targetEntity=Cible::class, mappedBy="mission")
     */
    private $cibles;

    /**
     * @ORM\OneToMany(targetEntity=Contact::class, mappedBy="mission")
     */
    private $contacts;

    /**
     * @ORM\ManyToOne(targetEntity=Pays::class, inversedBy="missions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pays;

    /**
     * @ORM\ManyToOne(targetEntity=TypeMission::class, inversedBy="missions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type_mission;

    /**
     * @ORM\ManyToOne(targetEntity=Statut::class, inversedBy="missions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $statut;

    /**
     * @ORM\ManyToOne(targetEntity=Specialite::class, inversedBy="missions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $specialite;

    /**
     * @ORM\ManyToMany(targetEntity=Agent::class, mappedBy="missions")
     */
    private $agents;

    public function __construct()
    {
        $this->planques = new ArrayCollection();
        $this->cibles = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->agents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getNomCode(): ?string
    {
        return $this->nom_code;
    }

    public function setNomCode(string $nom_code): self
    {
        $this->nom_code = $nom_code;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

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
            $planque->setMission($this);
        }

        return $this;
    }

    public function removePlanque(Planque $planque): self
    {
        if ($this->planques->removeElement($planque)) {
            // set the owning side to null (unless already changed)
            if ($planque->getMission() === $this) {
                $planque->setMission(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cible>
     */
    public function getCibles(): Collection
    {
        return $this->cibles;
    }

    public function addCible(Cible $cible): self
    {
        if (!$this->cibles->contains($cible)) {
            $this->cibles[] = $cible;
            $cible->setMission($this);
        }

        return $this;
    }

    public function removeCible(Cible $cible): self
    {
        if ($this->cibles->removeElement($cible)) {
            // set the owning side to null (unless already changed)
            if ($cible->getMission() === $this) {
                $cible->setMission(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setMission($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getMission() === $this) {
                $contact->setMission(null);
            }
        }

        return $this;
    }

    

    public function getPays(): ?Pays
    {
        return $this->pays;
    }

    public function setPays(?Pays $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getTypeMission(): ?TypeMission
    {
        return $this->type_mission;
    }

    public function setTypeMission(?TypeMission $type_mission): self
    {
        $this->type_mission = $type_mission;

        return $this;
    }

    public function getStatut(): ?Statut
    {
        return $this->statut;
    }

    public function setStatut(?Statut $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getSpecialite(): ?Specialite
    {
        return $this->specialite;
    }

    public function setSpecialite(?Specialite $specialite): self
    {
        $this->specialite = $specialite;

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
            $agent->addMission($this);
        }

        return $this;
    }

    public function removeAgent(Agent $agent): self
    {
        if ($this->agents->removeElement($agent)) {
            $agent->removeMission($this);
        }

        return $this;
    }
}

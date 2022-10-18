<?php

namespace App\Entity;

use App\Repository\PaysRepository;
// Import avec un alias afin de réduire la verbosité de nos validations
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaysRepository::class)
 */
class Pays
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
    *  @Assert\Length(
     *     max = 50,
     *     maxMessage = "Ce nom est trop long"
     * )
     * @Assert\NotBlank(message = "Le nom ne peut être vide.")
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=Cible::class, mappedBy="nationalite")
     */
    private $cibles;

    /**
     * @ORM\OneToMany(targetEntity=Contact::class, mappedBy="nationalite")
     */
    private $contacts;

    /**
     * @ORM\OneToMany(targetEntity=Mission::class, mappedBy="pays")
     */
    private $missions;

    /**
     * @ORM\OneToMany(targetEntity=Agent::class, mappedBy="nationalite")
     */
    private $agents;

    public function __construct()
    {
        $this->cibles = new ArrayCollection();
        $this->contacts = new ArrayCollection();
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
            $cible->setNationalite($this);
        }

        return $this;
    }

    public function removeCible(Cible $cible): self
    {
        if ($this->cibles->removeElement($cible)) {
            // set the owning side to null (unless already changed)
            if ($cible->getNationalite() === $this) {
                $cible->setNationalite(null);
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
            $contact->setNationalite($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getNationalite() === $this) {
                $contact->setNationalite(null);
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
            $mission->setPays($this);
        }

        return $this;
    }

    public function removeMission(Mission $mission): self
    {
        if ($this->missions->removeElement($mission)) {
            // set the owning side to null (unless already changed)
            if ($mission->getPays() === $this) {
                $mission->setPays(null);
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
            $agent->setNationalite($this);
        }

        return $this;
    }

    public function removeAgent(Agent $agent): self
    {
        if ($this->agents->removeElement($agent)) {
            // set the owning side to null (unless already changed)
            if ($agent->getNationalite() === $this) {
                $agent->setNationalite(null);
            }
        }

        return $this;
    }
}

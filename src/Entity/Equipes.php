<?php

namespace App\Entity;

use App\Repository\EquipesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=EquipesRepository::class)
 */
class Equipes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\ManyToOne(targetEntity=Joueurs::class, inversedBy="joueurs", cascade={"remove"})
     * @ORM\JoinColumn(nullable=true)
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @Assert\Regex(
     *     pattern     = "/^[a-z]+$/i"
     * )
     * @ORM\Column(type="string", length=300)
     */
    private $nom;

    /**
     *
     * @ORM\Column(type="integer")
     */
    private $nbr_vic;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbr_per;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbr_null;

    /**
     * @ORM\OneToMany(targetEntity=Joueurs::class, mappedBy="equipes",cascade={"persist"})
     */
    private $joueur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $suspension;
    /**
     * @ORM\ManyToOne(targetEntity=Matchs::class, inversedBy="equipe")
     */
    private $matchs;

    /**
     * @ORM\OneToMany(targetEntity=Matchs::class, mappedBy="equipe1",cascade={"persist"})
     */
    private $match1;

     /**
     * @ORM\OneToMany(targetEntity=Matchs::class, mappedBy="equipe2",cascade={"persist"})
     */
    private $match2;

    public function __construct()
    {
        $this->joueur = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getNom(): ?string
    {
        return $this->nom;
    }
    public function __toString()
    {
        return $this->getNom();
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getNbrVic(): ?int
    {
        return $this->nbr_vic;
    }

    public function setNbrVic(int $nbr_vic): self
    {
        $this->nbr_vic = $nbr_vic;

        return $this;
    }

    public function getNbrPer(): ?int
    {
        return $this->nbr_per;
    }

    public function setNbrPer(int $nbr_per): self
    {
        $this->nbr_per = $nbr_per;

        return $this;
    }

    public function getNbrNull(): ?int
    {
        return $this->nbr_null;
    }

    public function setNbrNull(int $nbr_null): self
    {
        $this->nbr_null = $nbr_null;

        return $this;
    }

    /**
     * @return Collection|Joueurs[]
     */
    public function getJoueur(): Collection
    {
        return $this->joueur;
    }

    public function addJoueur(Joueurs $joueur): self
    {
        if (!$this->joueur->contains($joueur)) {
            $this->joueur[] = $joueur;
            $joueur->setEquipes($this);
        }

        return $this;
    }

    public function removeJoueur(Joueurs $joueur): self
    {
        if ($this->joueur->removeElement($joueur)) {
            // set the owning side to null (unless already changed)
            if ($joueur->getEquipes() === $this) {
                $joueur->setEquipes(null);
            }
        }

        return $this;
    }

    public function getSuspension(): ?bool
    {
        return $this->suspension;
    }

    public function setSuspension(bool $suspension): self
    {
        $this->suspension = $suspension;

        return $this;
    }
    
    public function getMatchs(): ?Matchs
    {
        return $this->matchs;
    }

    public function setMatchs(?Matchs $matchs): self
    {
        $this->matchs = $matchs;

        return $this;
    }
    /**
     * @return Collection<int, Matchs>
     */
    public function getMatch1(): Collection
    {
        return $this->match1;
    }

    public function addMatch1(Matchs $match): self
    {
        if (!$this->match1->contains($match)) {
            $this->match1[] = $match;
            $match->setEquipe1($this);
        }

        return $this;
    }
    /**
     * @return Collection<int, Matchs>
     */
    public function getMatch2(): Collection
    {
        return $this->match2;
    }

    public function addMatch2(Matchs $match): self
    {
        if (!$this->match2->contains($match)) {
            $this->match2[] = $match;
            $match->setEquipe2($this);
        }

        return $this;
    }
}

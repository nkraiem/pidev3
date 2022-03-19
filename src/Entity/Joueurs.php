<?php

namespace App\Entity;

use App\Repository\JoueursRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=JoueursRepository::class)
 */
class Joueurs
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Regex(
     *     pattern     = "/^[a-zA-Z]+$/i",
     *     htmlPattern = "[a-zA-Z]+"
     * )
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\Regex(
     *     pattern     = "/^[a-zA-Z]+$/i"
     * )
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=60)
     *  @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/",
     *     message="not_valid_email"
     *)
     */
    private $email;

    /**
     * @Assert\Positive
     * @ORM\Column(type="integer",nullable=false)

     */
    private $numero;

    /**
     * @Assert\Positive
     * @ORM\Column(type="integer")
     */
    private $nbr_partie_jouer;

    /**
     * @ORM\ManyToOne(targetEntity=Equipes::class, inversedBy="joueur")
     */
    private $equipes;


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

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getNbrPartieJouer(): ?int
    {
        return $this->nbr_partie_jouer;
    }

    public function setNbrPartieJouer(int $nbr_partie_jouer): self
    {
        $this->nbr_partie_jouer = $nbr_partie_jouer;

        return $this;
    }

    public function getEquipes(): ?Equipes
    {
        return $this->equipes;
    }

    public function setEquipes(?Equipes $equipes): self
    {
        $this->equipes = $equipes;

        return $this;
    }

}

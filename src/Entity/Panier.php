<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PanierRepository::class)
 */
class Panier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $prix;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=ProduitPanier::class, mappedBy="panier", cascade={"persist", "remove"})
     */
    private $produitPaniers;

    public function __construct()
    {
        $this->produitPaniers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|ProduitPanier[]
     */
    public function getProduitPaniers(): Collection
    {
        return $this->produitPaniers;
    }

    public function addProduitPanier(ProduitPanier $produitPanier): self
    {
        if (!$this->produitPaniers->contains($produitPanier)) {
            $this->produitPaniers[] = $produitPanier;
            $produitPanier->setPanier($this);
        }

        return $this;
    }

    public function removeProduitPanier(ProduitPanier $produitPanier): self
    {
        if ($this->produitPaniers->removeElement($produitPanier)) {
            // set the owning side to null (unless already changed)
            if ($produitPanier->getPanier() === $this) {
                $produitPanier->setPanier(null);
            }
        }

        return $this;
    }

    public function __toString(){
        return $this->getUser()->getName();
    }
}

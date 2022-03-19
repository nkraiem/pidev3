<?php

namespace App\Entity;

use App\Repository\ProduitPanierRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProduitPanierRepository::class)
 */
class ProduitPanier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Panier::class, inversedBy="produitPaniers")
     */
    private $panier;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="produitPaniers")
     */
    private $produit;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $contiter;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): self
    {
        $this->panier = $panier;

        return $this;
    }

    public function getProduit(): ?Product
    {
        return $this->produit;
    }

    public function setProduit(?Product $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getContiter(): ?int
    {
        return $this->contiter;
    }

    public function setContiter(?int $contiter): self
    {
        $this->contiter = $contiter;

        return $this;
    }
}

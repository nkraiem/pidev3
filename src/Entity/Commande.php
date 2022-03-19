<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Commande
 *
 * @ORM\Table(name="commande", indexes={@ORM\Index(name="produit", columns={"produit"})})
 * @ORM\Entity
 */
class Commande
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="quantite", type="integer", nullable=false)
     * @Assert\Range(min=0,max=30)
     */
    private $quantite;

    /**
     * @var string
     *
     * @ORM\Column(name="date", type="string", length=225, nullable=false)
     */
    private $date;

    /**
     * @var \Produit
     *
     * @ORM\ManyToOne(targetEntity="Produit")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="produit", referencedColumnName="id")
     * })
     */
    private $produit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        /**
         * @Assert\Date
         * @var string A "Y-m-d" formatted value
         */
        $this->date = $date;

        return $this;
    }

    public function getProduit()
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit)
    {
        $this->produit = $produit;

        return $this;
    }

}

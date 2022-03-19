<?php

namespace App\Entity;

use App\Repository\HistoriqueRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HistoriqueRepository::class)
 */
class Historique
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbE;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbG;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbS;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbB;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbTotal;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbTG;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbE(): ?int
    {
        return $this->nbE;
    }

    public function setNbE(int $nbE): self
    {
        $this->nbE = $nbE;

        return $this;
    }

    public function getNbG(): ?int
    {
        return $this->nbG;
    }

    public function setNbG(int $nbG): self
    {
        $this->nbG = $nbG;

        return $this;
    }

    public function getNbS(): ?int
    {
        return $this->nbS;
    }

    public function setNbS(int $nbS): self
    {
        $this->nbS = $nbS;

        return $this;
    }

    public function getNbB(): ?int
    {
        return $this->nbB;
    }

    public function setNbB(int $nbB): self
    {
        $this->nbB = $nbB;

        return $this;
    }

    public function getNbTotal(): ?int
    {
        return $this->nbTotal;
    }

    public function setNbTotal(int $nbTotal): self
    {
        $this->nbTotal = $nbTotal;

        return $this;
    }

    public function getNbTG(): ?int
    {
        return $this->nbTG;
    }

    public function setNbTG(int $nbTG): self
    {
        $this->nbTG = $nbTG;

        return $this;
    }
}

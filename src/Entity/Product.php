<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @Vich\Uploadable
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Name is required")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="product_images", fileNameProperty="image")
     * @var File
     */
    private $imageFile;


    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Descreption is required")
     */
    private $Descreption;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="Prix is required")
     */
    private $prix;

    /**
     * @ORM\OneToMany(targetEntity=ProduitPanier::class, mappedBy="produit")
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getImage()
    {
        return $this->image;
    }


    public function getDescreption(): ?string
    {
        return $this->Descreption;
    }

    public function setDescreption(string $Descreption): self
    {
        $this->Descreption = $Descreption;

        return $this;
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
            $produitPanier->setProduit($this);
        }

        return $this;
    }

    public function removeProduitPanier(ProduitPanier $produitPanier): self
    {
        if ($this->produitPaniers->removeElement($produitPanier)) {
            // set the owning side to null (unless already changed)
            if ($produitPanier->getProduit() === $this) {
                $produitPanier->setProduit(null);
            }
        }

        return $this;
    }

    public function __toString(){
        return $this->name;
    }
}

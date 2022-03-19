<?php

namespace App\Entity;

use App\Repository\PublicationsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=PublicationsRepository::class)
 */
class Publications
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    private $titrePub;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank
     */
    private $contenuPub;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\NotBlank
     */
    private $datePub;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    private $autheurPub;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $imagePub;

   

    /**
     * @ORM\OneToMany(targetEntity=Commantaires::class, mappedBy="publication",cascade={"persist"})
     */
    private $commantaires;

    /**
     * @ORM\OneToMany(targetEntity=Like::class, mappedBy="publication")
     */
    private $likes;



    public function __construct()
    {
        $this->commantaires = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitrePub(): ?string
    {
        return $this->titrePub;
    }

    public function setTitrePub(?string $titrePub): self
    {
        $this->titrePub = $titrePub;

        return $this;
    }
    public function __toString()
    {
        return $this->getTitrePub();
    }


    public function getContenuPub(): ?string
    {
        return $this->contenuPub;
    }

    public function setContenuPub(?string $contenuPub): self
    {
        $this->contenuPub = $contenuPub;

        return $this;
    }

    public function getDatePub(): ?\DateTimeInterface
    {
        return $this->datePub;
    }

    public function setDatePub(?\DateTimeInterface $datePub): self
    {
        $this->datePub = $datePub;

        return $this;
    }

    public function getAutheurPub(): ?string
    {
        return $this->autheurPub;
    }

    public function setAutheurPub(?string $autheurPub): self
    {
        $this->autheurPub = $autheurPub;

        return $this;
    }

    public function getImagePub()
    {
        return $this->imagePub;
    }

    public function setImagePub($imagePub): self
    {
        $this->imagePub = $imagePub;

        return $this;
    }

    

    /**
     * @return Collection<int, Commantaires>
     */
    public function getCommantaires(): Collection
    {
        return $this->commantaires;
    }

    public function addCommantaire(Commantaires $commantaire): self
    {
        if (!$this->commantaires->contains($commantaire)) {
            $this->commantaires[] = $commantaire;
            $commantaire->setPublication($this);
        }

        return $this;
    }

    public function removeCommantaire(Commantaires $commantaire): self
    {
        if ($this->commantaires->removeElement($commantaire)) {
            // set the owning side to null (unless already changed)
            if ($commantaire->getPublication() === $this) {
                $commantaire->setPublication(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setPublication($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getPublication() === $this) {
                $like->setPublication(null);
            }
        }

        return $this;
    }

}

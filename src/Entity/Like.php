<?php

namespace App\Entity;

use App\Repository\LikeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LikeRepository::class)
 * @ORM\Table(name="`like`")
 */
class Like
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity=Publications::class, inversedBy="likes")
     */
    private $publication;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rate;



    public function getId(): ?int
    {
        return $this->id;
    }

   

    public function getPublication(): ?Publications
    {
        return $this->publication;
    }

    public function setPublication(?Publications $publication): self
    {
        $this->publication = $publication;

        return $this;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(?int $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

}

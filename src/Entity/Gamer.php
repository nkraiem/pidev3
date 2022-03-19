<?php

namespace App\Entity;

use App\Repository\GamerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=GamerRepository::class)
 * @Vich\Uploadable
 */
class Gamer extends User
{

}

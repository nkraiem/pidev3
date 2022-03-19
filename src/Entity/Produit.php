<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Produit
 *
 * @ORM\Table(name="produit")
 * @ORM\Entity
 */
class Produit
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
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=225, nullable=false)
     * {% if pageCount > 1 %}
    <nav aria-label="pagination">
    <ul class="pagination job-pagination justify-content-center mb-0 mt-4 pt-2">
    <li class="page-item">
    {% if previous is defined %}
    <a rel="prev" class="page-link"
    href="{{ path(route, query|merge({(pageParameterName): previous})) }}">
    <i class="mdi mdi-chevron-left text-dark"></i>
    </a>
    {% else %}
    <p class="page-link disabled bg-light">
    <i class="mdi mdi-chevron-left text-muted"></i>
    </p>
    {% endif %}
    </li>


    {% if current == first %}
    <li class="page-item active">
    <a class="page-link" aria-label="Page {{ current }}" aria-current="page"
    href="{{ path(route, query|merge({(pageParameterName): first})) }}">1
    </a>
    </li>
    {% else %}
    <li class="page-item">
    <a class="page-link" href="{{ path(route, query|merge({(pageParameterName): first})) }}">1</a>
    </li>
    {% endif %}

    {% if pagesInRange[0] - first >= 2 %}
    <li class="page-item">
    <span class="pagination-ellipsis">&hellip;</span>
    </li>
    {% endif %}

    {% for page in pagesInRange %}
    {% if first != page and page != last %}
    {% if page == current %}
    <li class="page-item active">
    <a class="page-link" aria-label="Page {{ current }}" aria-current="page"
    href="{{ path(route, query|merge({(pageParameterName): page})) }}">{{ page }}
    </a>
    </li>
    {% else %}
    <li class="page-item">
    <a class="page-link" aria-label="Goto page {{ page }}"
    href="{{ path(route, query|merge({(pageParameterName): page})) }}">{{ page }}
    </a>
    </li>
    {% endif %}
    {% endif %}
    {% endfor %}

    {% if last - pagesInRange[pagesInRange|length - 1] >= 2 %}
    <li class="page-item">
    <span class="pagination-ellipsis">&hellip;</span>
    </li>
    {% endif %}

    {% if current == last %}
    <li class="page-item active">
    <a class="page-link " aria-label="Page {{ current }}" aria-current="page"
    href="{{ path(route, query|merge({(pageParameterName): last})) }}">{{ last }}</a>
    </li>
    {% else %}
    <li class="page-item">
    <a class="page-link"
    href="{{ path(route, query|merge({(pageParameterName): last})) }}">{{ last }}</a>
    </li>
    {% endif %}


    {% if next is defined %}
    <li class="page-item">
    <a rel="next" class="page-link"
    href="{{ path(route, query|merge({(pageParameterName): next})) }}">
    <i class="mdi mdi-chevron-right"></i>
    </a>
    </li>
    {% else %}
    <p class="page-link disabled bg-light">
    <i class="mdi mdi-chevron-right text-muted"></i>
    </p>
    {% endif %}
    </ul>
    </nav>
    {% endif %}
     */
    private $nom;

    /**
     * @var int
     *
     * @ORM\Column(name="prix", type="integer", nullable=false)
     *{% if pageCount > 1 %}
    <nav aria-label="pagination">
    <ul class="pagination job-pagination justify-content-center mb-0 mt-4 pt-2">
    <li class="page-item">
    {% if previous is defined %}
    <a rel="prev" class="page-link"
    href="{{ path(route, query|merge({(pageParameterName): previous})) }}">
    <i class="mdi mdi-chevron-left text-dark"></i>
    </a>
    {% else %}
    <p class="page-link disabled bg-light">
    <i class="mdi mdi-chevron-left text-muted"></i>
    </p>
    {% endif %}
    </li>


    {% if current == first %}
    <li class="page-item active">
    <a class="page-link" aria-label="Page {{ current }}" aria-current="page"
    href="{{ path(route, query|merge({(pageParameterName): first})) }}">1
    </a>
    </li>
    {% else %}
    <li class="page-item">
    <a class="page-link" href="{{ path(route, query|merge({(pageParameterName): first})) }}">1</a>
    </li>
    {% endif %}

    {% if pagesInRange[0] - first >= 2 %}
    <li class="page-item">
    <span class="pagination-ellipsis">&hellip;</span>
    </li>
    {% endif %}

    {% for page in pagesInRange %}
    {% if first != page and page != last %}
    {% if page == current %}
    <li class="page-item active">
    <a class="page-link" aria-label="Page {{ current }}" aria-current="page"
    href="{{ path(route, query|merge({(pageParameterName): page})) }}">{{ page }}
    </a>
    </li>
    {% else %}
    <li class="page-item">
    <a class="page-link" aria-label="Goto page {{ page }}"
    href="{{ path(route, query|merge({(pageParameterName): page})) }}">{{ page }}
    </a>
    </li>
    {% endif %}
    {% endif %}
    {% endfor %}

    {% if last - pagesInRange[pagesInRange|length - 1] >= 2 %}
    <li class="page-item">
    <span class="pagination-ellipsis">&hellip;</span>
    </li>
    {% endif %}

    {% if current == last %}
    <li class="page-item active">
    <a class="page-link " aria-label="Page {{ current }}" aria-current="page"
    href="{{ path(route, query|merge({(pageParameterName): last})) }}">{{ last }}</a>
    </li>
    {% else %}
    <li class="page-item">
    <a class="page-link"
    href="{{ path(route, query|merge({(pageParameterName): last})) }}">{{ last }}</a>
    </li>
    {% endif %}


    {% if next is defined %}
    <li class="page-item">
    <a rel="next" class="page-link"
    href="{{ path(route, query|merge({(pageParameterName): next})) }}">
    <i class="mdi mdi-chevron-right"></i>
    </a>
    </li>
    {% else %}
    <p class="page-link disabled bg-light">
    <i class="mdi mdi-chevron-right text-muted"></i>
    </p>
    {% endif %}
    </ul>
    </nav>
    {% endif %}
     */
    private $prix;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=225, nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=225, nullable=false)
     * @Assert\File(mimeTypes={"image/jpeg"})
     */
    private $image;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $Qrcode;

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

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }
    public function __toString() {
        return $this->nom;
    }

    public function getQrcode(): ?string
    {
        return $this->Qrcode;
    }

    public function setQrcode(?string $Qrcode): self
    {
        $this->Qrcode = $Qrcode;

        return $this;
    }


}

<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductsRepository")
 */
class Products
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Name;

    /**
     * @ORM\Column(type="float")
     */
    private $Price;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $Description;

    /**
     * @ORM\Column(type="blob")
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=750, nullable=true)
     */
    private $galleryLink;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Categories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Category;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Opinions", mappedBy="products")
     */
    private $Opinions;

    public function __construct()
    {
        $this->Opinions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->Price;
    }

    public function setPrice(float $Price): self
    {
        $this->Price = $Price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getImage()
    {
        return stream_get_contents($this->image);
    }

    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getGalleryLink(): ?string
    {
        return $this->galleryLink;
    }

    public function setGalleryLink(?string $galleryLink): self
    {
        $this->galleryLink = $galleryLink;

        return $this;
    }

    public function getCategory(): ?Categories
    {
        return $this->Category;
    }

    public function setCategory(?Categories $Category): self
    {
        $this->Category = $Category;

        return $this;
    }

    /**
     * @return Collection|Opinions[]
     */
    public function getOpinions(): Collection
    {
        return $this->Opinions;
    }

    public function addOpinion(Opinions $opinion): self
    {
        if (!$this->Opinions->contains($opinion)) {
            $this->Opinions[] = $opinion;
            $opinion->setProducts($this);
        }

        return $this;
    }

    public function removeOpinion(Opinions $opinion): self
    {
        if ($this->Opinions->contains($opinion)) {
            $this->Opinions->removeElement($opinion);
            // set the owning side to null (unless already changed)
            if ($opinion->getProducts() === $this) {
                $opinion->setProducts(null);
            }
        }

        return $this;
    }
}

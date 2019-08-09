<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UsersRepository")
 */
class Users
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $Username;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $Password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Email;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $ResetPass;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Deals", mappedBy="customer")
     */
    private $deals;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserDetails", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $Details;

    public function __construct()
    {
        $this->deals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->Username;
    }

    public function setUsername(string $Username): self
    {
        $this->Username = $Username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->Password;
    }

    public function setPassword(string $Password): self
    {
        $this->Password = $Password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): self
    {
        $this->Email = $Email;

        return $this;
    }

    public function getResetPass(): ?string
    {
        return $this->ResetPass;
    }

    public function setResetPass(string $ResetPass): self
    {
        $this->ResetPass = $ResetPass;

        return $this;
    }

    /**
     * @return Collection|Deals[]
     */
    public function getDeals(): Collection
    {
        return $this->deals;
    }

    public function addDeal(Deals $deal): self
    {
        if (!$this->deals->contains($deal)) {
            $this->deals[] = $deal;
            $deal->setCustomer($this);
        }

        return $this;
    }

    public function removeDeal(Deals $deal): self
    {
        if ($this->deals->contains($deal)) {
            $this->deals->removeElement($deal);
            // set the owning side to null (unless already changed)
            if ($deal->getCustomer() === $this) {
                $deal->setCustomer(null);
            }
        }

        return $this;
    }

    public function getDetails(): ?UserDetails
    {
        return $this->Details;
    }

    public function setDetails(UserDetails $Details): self
    {
        $this->Details = $Details;

        return $this;
    }
}

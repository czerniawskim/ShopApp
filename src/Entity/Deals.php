<?php

namespace App\Entity;

use App\Repository\DealsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DealsRepository::class)
 */
class Deals
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="array")
     */
    private $list = [];

    /**
     * @ORM\Column(type="datetime")
     */
    private $madeAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="deals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $buyer;

    /**
     * @ORM\Column(type="float")
     */
    private $total;

    /**
     * @ORM\Column(type="array")
     */
    private $payment = [];

    /**
     * @ORM\Column(type="array")
     */
    private $delivery = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getList(): ?array
    {
        return $this->list;
    }

    public function setList(array $list): self
    {
        $this->list = $list;

        return $this;
    }

    public function getMadeAt(): ?\DateTimeInterface
    {
        return $this->madeAt;
    }

    public function setMadeAt(\DateTimeInterface $madeAt): self
    {
        $this->madeAt = $madeAt;

        return $this;
    }

    public function getBuyer(): ?User
    {
        return $this->buyer;
    }

    public function setBuyer(?User $buyer): self
    {
        $this->buyer = $buyer;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getPayment(): ?array
    {
        return $this->payment;
    }

    public function setPayment(array $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getDelivery(): ?array
    {
        return $this->delivery;
    }

    public function setDelivery(array $delivery): self
    {
        $this->delivery = $delivery;

        return $this;
    }
}

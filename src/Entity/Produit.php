<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MenuRestaurant $menuRestaurant = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isActive = false;

    #[ORM\Column]
    private ?int $number = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reduction = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $textSpecial = null;

    #[ORM\Column]
    private ?bool $isReduction = false;

    #[ORM\Column]
    private ?bool $isOutOffStock = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $label = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return strip_tags($this->description);
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getMenuRestaurant(): ?MenuRestaurant
    {
        return $this->menuRestaurant;
    }

    public function setMenuRestaurant(?MenuRestaurant $menuRestaurant): static
    {
        $this->menuRestaurant = $menuRestaurant;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getReduction(): ?string
    {
        return $this->reduction;
    }

    public function setReduction(?string $reduction): static
    {
        $this->reduction = $reduction;

        return $this;
    }

    public function getTextSpecial(): ?string
    {
        return $this->textSpecial;
    }

    public function setTextSpecial(?string $textSpecial): static
    {
        $this->textSpecial = $textSpecial;

        return $this;
    }

    public function isIsReduction(): ?bool
    {
        return $this->isReduction;
    }

    public function setIsReduction(bool $isReduction): static
    {
        $this->isReduction = $isReduction;

        return $this;
    }

    public function isIsOutOffStock(): ?bool
    {
        return $this->isOutOffStock;
    }

    public function setIsOutOffStock(bool $isOutOffStock): static
    {
        $this->isOutOffStock = $isOutOffStock;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): static
    {
        $this->label = $label;

        return $this;
    }
}

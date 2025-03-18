<?php

namespace App\Entity;

use App\Repository\DropdownMenuRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DropdownMenuRepository::class)]
class DropdownMenu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $label = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $link = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isParameter = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isActive = false;

    #[ORM\ManyToOne(inversedBy: 'dropdownMenus')]
    private ?MenuHeader $menuHeader = null;

    #[ORM\Column]
    private ?bool $isUserLogout = false;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;

        return $this;
    }

    public function isIsParameter(): ?bool
    {
        return $this->isParameter;
    }

    public function setIsParameter(?bool $isParameter): static
    {
        $this->isParameter = $isParameter;

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

    public function getMenuHeader(): ?MenuHeader
    {
        return $this->menuHeader;
    }

    public function setMenuHeader(?MenuHeader $menuHeader): static
    {
        $this->menuHeader = $menuHeader;

        return $this;
    }

    public function isIsUserLogout(): ?bool
    {
        return $this->isUserLogout;
    }

    public function setIsUserLogout(bool $isUserLogout): static
    {
        $this->isUserLogout = $isUserLogout;

        return $this;
    }
}

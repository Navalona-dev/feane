<?php

namespace App\Entity;

use App\Repository\MenuHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuHeaderRepository::class)]
class MenuHeader
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

    #[ORM\OneToMany(mappedBy: 'menuHeader', targetEntity: DropdownMenu::class)]
    private Collection $dropdownMenus;

    #[ORM\Column(nullable: true)]
    private ?bool $isActive = false;

    public function __construct()
    {
        $this->dropdownMenus = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name; 
    }

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

    /**
     * @return Collection<int, DropdownMenu>
     */
    public function getDropdownMenus(): Collection
    {
        return $this->dropdownMenus;
    }

    public function addDropdownMenu(DropdownMenu $dropdownMenu): static
    {
        if (!$this->dropdownMenus->contains($dropdownMenu)) {
            $this->dropdownMenus->add($dropdownMenu);
            $dropdownMenu->setMenuHeader($this);
        }

        return $this;
    }

    public function removeDropdownMenu(DropdownMenu $dropdownMenu): static
    {
        if ($this->dropdownMenus->removeElement($dropdownMenu)) {
            // set the owning side to null (unless already changed)
            if ($dropdownMenu->getMenuHeader() === $this) {
                $dropdownMenu->setMenuHeader(null);
            }
        }

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
}
